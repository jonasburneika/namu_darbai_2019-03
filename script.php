<?php
if (isset($_POST['file'])){
    $content = file_get_contents($_POST['file']);
    $jsonData = json_decode($content,true);
    echo calculateSMSList($jsonData);
} else {
    $content = file_get_contents('data/input.json');
    $jsonData = json_decode($content,true);
    echo calculateSMSList($jsonData);
}

function calculateSMSList($jsonData){
    if (isset($jsonData['required_income']) && $jsonData['required_income'] > 0){
        $requiredIncome = $jsonData['required_income'];
    } else {
        return 'error no Required_income';
    }

    $smsData = [];
    foreach($jsonData['sms_list'] as $smsPriceData){
        $smsData[] = [
            'income'        => $smsPriceData['income'], 
            'price'         => $smsPriceData['price'], 
            'effectivenes'  => round(($smsPriceData['income']/$smsPriceData['price']-1),3)*100,
            'damage'        => (((round(($smsPriceData['income']/$smsPriceData['price']-1),3)*100))/$smsPriceData['price']),
        ];
    }
    usort($smsData, function ($item1, $item2) {
        return $item2['damage'] <=> $item1['damage'];
    });
    // echo '<pre>';
    // var_dump($smsData);
    // echo '</pre>';
   
    if (isset($jsonData['max_messages']) && $jsonData['max_messages'] > 0){
        $maxMessages = $jsonData['max_messages'];
        if ($requiredIncome > $maxMessages * $smsData[0]['income']){
            return 'error: We can\'t pay given sum in such amount of sms'; 
        }
    }

    $payments = [];
    
    $leftToPay = $requiredIncome;
    $smsVariations = count($jsonData['sms_list']);
    $x=0;
    $payments[$i]['income'] = 0;
    $payments[$i]['opperations'] = 0;
    $payments[$i]['price'] = 0;
    
    while($leftToPay > 0){
        if ($x>50){
            break;
        }
        $payment = $smsData[0];
        if ($leftToPay-$payment['income'] > 0){
            $leftToPay = $leftToPay-$payment['income'];
        } else {
            $diff = abs($leftToPay-$payment['income']);
            $tmpList = [];
            foreach($smsData as $rowNo => $possiblePayment){
                if (isset($maxMessages)){
                    if ($maxMessages - $x == 1 && ($diff-$possiblePayment['income']) < 0 ){
                        $tmpList[$rowNo-1] = [
                            'key'   => $rowNo,
                            'diff'  => abs($diff-$possiblePayment['income']),
                        ];
                    }
                } else {
                    if ($rowNo == 0){continue;}
                    $tmpList[$rowNo-1] = [
                        'key'   => $rowNo,
                        'diff'  => abs($diff-$possiblePayment['income']),
                    ];
                }
            }
            usort($tmpList, function ($item1, $item2) {
                return $item2['diff'] <=> $item1['diff'];
            });
            $payment = $smsData[$tmpList[0]['key']];
            $leftToPay = $leftToPay-$payment['income'];
        }
        $payments['payments'][] = $payment['price'];
        $payments['income'] += $payment['income'];
        $payments['price'] += $payment['price'];
        $payments['opperations'] += 1;
        $x++;
    }
    $payments['effectivenes'] = (1-round($payments['income']/$payments['price'],3));
    
    ?>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Operations</th>
            <th scope="col">Income</th>
            <th scope="col">Price</th>
            <th scope="col">Effectivenes</th>
            <th scope="col">payments</th>
            </tr>
        </thead>
        <tbody>
            <?php
            echo '<tr>';
                echo '<th scope="row">' . $key . '</th>';
                echo '<td>' . count($payments['payments']) . '</td>';
                echo '<td>' . $payments['income'] . '</td>';
                echo '<td>' . $payments['price'] . '</td>';
                echo '<td>' . $payments['effectivenes'] . '</td>';
                echo '<td>[ ' . implode(' , ',$payments['payments']) . ' ]</td>';
            echo '</tr>';
            
            ?>
        </tbody>
    </table>
    <?php

}


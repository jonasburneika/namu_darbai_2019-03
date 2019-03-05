<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
        <section class="jumbotron">
            <div class="container">
                <div class="form-group">
                    <label for="jsonFileSelector">Please select your JSON file</label>
                    <select class="form-control" id="jsonFileSelector">
                        <?php
                            
                            $files = scandir('data');
                            foreach($files as $fileName){
                                $fileName = strtolower($fileName);
                                if (strpos($fileName,".json") > 0){
                                    echo '<option value="'.$fileName.'">' . $fileName . '</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="col-12">
                    <pre>
                        <span id="result"></span>
                    </pre>
                </div>
            </div>
        </section>

        <script>
            document.getElementById("jsonFileSelector").addEventListener("change", function($this){
                var jsonFile = document.getElementById("jsonFileSelector").value;
                postAjax('script.php', 'file=data/'+jsonFile, function(data){ 
                    var resultPlace = document.getElementById("result");
                    resultPlace.innerHTML = data;
                });
            });

            function postAjax(url, data, success) {
                var params = typeof data == 'string' ? data : Object.keys(data).map(
                        function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
                    ).join('&');
            
                var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
                xhr.open('POST', url);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
                };
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send(params);
                return xhr;
            }
        </script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>

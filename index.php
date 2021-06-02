<?php
$conn = mysqli_connect("localhost", "root", "", "barcode");
// Check connection
if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Print host information
echo "Connect Successfully. Host info: " . mysqli_get_host_info($conn);

$id = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <style>
        #interactive.viewport {
            position: relative;
            width: 100%;
            height: auto;
            overflow: hidden;
            text-align: center;
        }

        #interactive.viewport>canvas,
        #interactive.viewport>video {
            max-width: 100%;
            width: 100%;
        }

        canvas.drawing,
        canvas.drawingBuffer {
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>

    <title>Document</title>
</head>

<body>
    <div class="container" style="margin-top: 2em;">

        <div class="row" style="margin-bottom: 2em;">
            <div class="col-lg-12">
                <h1>Web Barcode Scanner,</h1>
                <h3>made by <strong>Hafiz</strong></h3>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="input-group">
                    <!-- <input id="scanner_input" class="form-control" placeholder="Click the button to scan an EAN..."
                        type="text" /> -->
                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#livestream_scanner">
                        Click the button to scan the Barcode.
                        <!-- <i class="fa fa-barcode"></i> -->
                    </button>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
        </div><!-- /.row -->

        <div style="margin-top: 2em;">
            <label>Result:</label>
            <pre><code id="result"><?php echo $id; ?></code></pre>
            <?php

            $exist = $conn->query("SELECT id FROM product WHERE id = '$id'");
            if ($exist->num_rows == 0 && $id != null) {
                echo "The id doesn't exist in the Database, try scanning again or";
            ?>
                <button class="btn btn-default" style="margin-left: 1rem;" type="button" data-toggle="modal" data-target="#addid">Click to add product into the Database </button>
                <?php
            } else {
                $sql = "SELECT id, name, type, price FROM product WHERE id='$id'";
                $result = $conn->query($sql);
                if ($id != null) {
                    // output data of each row
                    while ($row = $result->fetch_assoc()) { ?>
                        <table class="table table-bordered" cellspacing='0'>
                            <tr>
                                <th>Product code</th>
                                <th>Product name</th>
                                <th>Product type</th>
                                <th>Price (RM)</th>
                            </tr>
                            <tbody id="rows">
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td> <?php echo $row["name"]; ?></td>
                                    <td>
                                        <?php echo $row["type"]; ?></td>
                                    <td> <?php printf("%.2f", $row["price"]) ?></td>
                                </tr>
                            </tbody>
                        </table><?php
                            }
                        } else {
                            echo "0 result";
                        }
                        $conn->close();
                    }
                                ?>
        </div>

        <div class="modal" id="livestream_scanner">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Barcode Scanner</h4>
                    </div>
                    <div class="modal-body" style="position: static">
                        <div id="interactive" class="viewport"></div>
                        <div class="error"></div>
                    </div>
                    <div class="modal-footer">
                        <select id="videoSource" class="pull-left">
                            <option value="enviroment" selected>Back</option>
                            <option value="user">Front</option>
                        </select>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" id="addid" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Adding Product ID: <?php echo $id; ?></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-inline" action="barcodeScanner.php" method="POST" onsubmit="return subprod()">
                        <input type="hidden" name="prodid" value="<?php echo $id; ?>">
                            <div class="form-group">
                                <label for="productname">Product Name</label>
                                <input type="text" class="form-control" name="productname" id="productname" placeholder="" required>
                            </div>
                            <div class="form-group">
                                <label for="producttype">Product Type</label>
                                <input type="text" class="form-control" name="producttype" id="producttype" placeholder="" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" name="price" id="price" placeholder="" required min="0" value="0" step="0.1">
                            </div>
                            <div class="form-group" style="margin-top: 1rem;">
                                <button type="submit" id="submitproduct" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                        <script>
                            function subprod() {
                                if (confirm("Are you sure you want to save the product?")) {
                                    return true;
                                } else {
                                    return false;
                                }
                            };
                        </script>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://a.kabachnik.info/assets/js/libs/quagga.min.js"></script>
    <script type="text/javascript">
        var id;
        var _scannerIsRunning = false;
        $(function() {
            // Create the QuaggaJS config object for the live stream
            var liveStreamConfig = {
                inputStream: {
                    type: "LiveStream",
                    constraints: {
                        width: {
                            min: 640
                        },
                        height: {
                            min: 480
                        },
                        aspectRatio: {
                            min: 1,
                            max: 100
                        },
                        facingMode: document.querySelector("#videoSource").value // or "user" for the front camera
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: navigator.hardwareConcurrency ?
                    navigator.hardwareConcurrency : 4,
                decoder: {
                    readers: [{
                        format: "ean_reader",
                        config: {}
                    }]
                },
                locate: true
            };
            // The fallback to the file API requires a different inputStream option.
            // The rest is the same
            var fileConfig = $.extend({}, liveStreamConfig, {
                inputStream: {
                    size: 800
                }
            });
            // Start the live stream scanner when the modal opens
            $("#livestream_scanner").on("shown.bs.modal", function(e) {
                Quagga.init(liveStreamConfig, function(err) {
                    if (err) {
                        $("#livestream_scanner .modal-body .error").html(
                            '<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle"></i> ' +
                            err.name +
                            "</strong>: " +
                            err.message +
                            "</div>"
                        );
                        _scannerIsRunning = false;
                        Quagga.stop();
                        return;
                    }
                    Quagga.start();
                    _scannerIsRunning = true;
                });
            });

            document.getElementById("videoSource").addEventListener("change", function() {
                if (_scannerIsRunning) {
                    Quagga.stop();
                }
                Quagga.init(liveStreamConfig, function(err) {
                    if (err) {
                        $("#livestream_scanner .modal-body .error").html(
                            '<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle"></i> ' +
                            err.name +
                            "</strong>: " +
                            err.message +
                            "</div>"
                        );
                        Quagga.stop();
                        return;
                    }
                    Quagga.start();
                });
            }, false);

            // Make sure, QuaggaJS draws frames an lines around possible
            // barcodes on the live stream
            Quagga.onProcessed(function(result) {
                var drawingCtx = Quagga.canvas.ctx.overlay,
                    drawingCanvas = Quagga.canvas.dom.overlay;

                if (result) {
                    if (result.boxes) {
                        drawingCtx.clearRect(
                            0,
                            0,
                            parseInt(drawingCanvas.getAttribute("width")),
                            parseInt(drawingCanvas.getAttribute("height"))
                        );
                        result.boxes
                            .filter(function(box) {
                                return box !== result.box;
                            })
                            .forEach(function(box) {
                                Quagga.ImageDebug.drawPath(box, {
                                    x: 0,
                                    y: 1
                                }, drawingCtx, {
                                    color: "green",
                                    lineWidth: 2
                                });
                            });
                    }

                    if (result.box) {
                        Quagga.ImageDebug.drawPath(result.box, {
                            x: 0,
                            y: 1
                        }, drawingCtx, {
                            color: "#00F",
                            lineWidth: 2
                        });
                    }
                    if (result.codeResult && result.codeResult.code) {
                        Quagga.ImageDebug.drawPath(
                            result.line, {
                                x: "x",
                                y: "y"
                            },
                            drawingCtx, {
                                color: "red",
                                lineWidth: 3
                            }
                        );
                    }
                }
            });
            // Once a barcode had been read successfully, stop quagga and
            // close the modal after a second to let the user notice where
            // the barcode had actually been found.
            Quagga.onDetected(function(result) {
                if (result.codeResult.code) {

                    // $("#scanner_input").val(result.codeResult.code);
                    id = result.codeResult.code;
                    console.log(id);

                    if (id != document.getElementById('result').innerHTML) {
                        window.location.href = "?id=" + id;
                        // document.getElementById('result').innerHTML = id;
                    }

                    Quagga.stop();
                    setTimeout(function() {
                        $("#livestream_scanner").modal("hide");
                    }, 1000);
                }
            });
            // Stop quagga in any case, when the modal is closed
            $("#livestream_scanner").on("hide.bs.modal", function() {
                if (Quagga) {
                    Quagga.stop();
                }
            });
            // Call Quagga.decodeSingle() for every file selected in the
            // file input
            $("#livestream_scanner input:file").on("change", function(e) {
                if (e.target.files && e.target.files.length) {
                    Quagga.decodeSingle(
                        $.extend({}, fileConfig, {
                            src: URL.createObjectURL(e.target.files[0])
                        }),
                        function(result) {
                            alert(result.codeResult.code);
                        }
                    );
                }
            });
        });
    </script>
</body>

</html>
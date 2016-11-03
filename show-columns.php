<?php
session_start();
include_once "includes-all.php";

$dbname = $_GET["database"];

$conn = get_connection($dbname);

$table_main = $_GET["table"];

$sql = "select * from information_schema.columns WHERE table_schema ='$dbname' AND TABLE_NAME = '$table_main'";
$result = $conn->query($sql);
$columns = $result->fetchAll(PDO::FETCH_ASSOC);


$sql = "SHOW TABLES";
$result = $conn->query($sql);
$tables = $result->fetchAll(PDO::FETCH_COLUMN);

//var_dump($columns);
?>


<div class="header">
    <div class="logo"><b>Data Generator v1.0</b></div>
    <div class="breadcrumb"><a href="list-database?server=<?php echo $_SESSION["server"] ?>">Home</a> / <a
            href="show-tables?database=<?php echo $dbname ?>"><?php echo $dbname ?></a> / <?php echo $table_main ?> /</div>
    <div style="width: 30px;float: right;margin-right: 60px;"><a href="logout">Logout</a></div>
</div>
<div class="left-nav">
    <ol>
        <?php foreach ($tables as $table) { ?>
            <li><a <?php if ($table == $table_main){ ?> class="link-active"
                                                        <?php } ?>href='show-columns?database=<?php echo $dbname . "&table=" . $table ?>'><?php echo $table ?></a>
            </li>
        <?php } ?>
    </ol>
</div>

<div class="main-body">
    <div class="column-description">
        <?php $i = 1; ?>
        <table id="column-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Column</th>
                <th>Type</th>
                <th>Example</th>
                <th>Options</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($columns as $column) { ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td>
                        <div class="column_name"><?php echo $column["COLUMN_NAME"] ?></div>
                    </td>
                    <td class="light-grey">
                        <input type="hidden" class="hidden-data-type"
                               value="<?php echo $column["DATA_TYPE"] ?>"/> <?php echo $column["COLUMN_TYPE"] ?>
                        <input type="hidden" class="hidden-column-key" value="<?php echo $column["COLUMN_KEY"] ?>">
                    </td>
                    <td>
                        <div class="select_column">
                            <select class="select_example">
                                <option value="">Select</option>
                                <optgroup label="Human Data">
                                    <option value="name">Names</option>
                                    <option value="phone">Phone</option>
                                    <option value="email">Email</option>
                                    <option value="date">Date / Date Time</option>
                                    <option value="company">Company</option>
                                    <option value="street">Street</option>
                                </optgroup>
                                <optgroup label="Text">
                                    <option value="short_text">Short Text</option>
                                    <option value="long_text">Long Text</option>
                                </optgroup>

                                <optgroup label="Number">
                                    <option value="number">Number</option>
                                    <option value="auto_increment">Auto Increment</option>
                                </optgroup>

                                <optgroup label="Extra">
                                    <option data-type="text" value="custom_list">Custom List</option>
                                    <option data-type="varchar" value="url">URL</option>
                                </optgroup>

                                <optgroup label="Geo">
                                    <option value="city">City</option>
                                    <option value="postal">Postal</option>
                                    <option value="region">Region</option>
                                    <option value="country">Country</option>
                                    <option value="lng">Longitude</option>
                                    <option value="lat">Latitude</option>
                                </optgroup>

                                <optgroup label="Login">
                                    <option value="username">Username
                                    </option>
                                    <option value="password">Password
                                    </option>
                                </optgroup>

                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="extra-options"></div>
                    </td>
                </tr>
                <?php $i++ ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div  class="generate-meter">
        <p><b>Generate Data</b></p>
        <p><input type="text" class="small-input" id="no_of_rows" value="1000"/> Rows <input type="button" id="btn-generate" value="Generate" onclick="generate_data()"/></p>
        <br/>
        <p><b>Speed</b></p>
        <span id="generate-speed"></span>
        <input type="button" value="Stop" id="btn-stop" class="btn" onclick="stop_generate()">
    </div>
    <div class="clearfix"></div>
    <p style="align-content: center"><b>Preview Data</b> <input type="button" id="btn-preview" class="btn" value="Preview" onclick="send_json('preview',this)"/></p>
    <div class="preview">
        <table id="preview-table">
            <thead>
            <tr>
                <?php foreach ($columns as $column) { ?>
                    <td><?php echo $column["COLUMN_NAME"] ?><br/><span
                            class="light-grey"><?php echo $column["COLUMN_TYPE"] ?></span></td>
                <?php } ?>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<script>

    var current_date = "<?php echo date('Y-m-d')?>";
    var select_example = $(".select_example");
    var column_name = $(".column_name");
    var hidden_data_type = $(".hidden-data-type");

    hidden_data_type.each(function () {
        re_arrange_column(this);
    });

    select_example.change(function () {
        select_example_change(this);
    });

    select_example.first().val("auto_increment");
    $(".extra-options").first().html("auto Increment");

    function re_arrange_column(thisObj) {

        var data_type = $(thisObj).val();
        var extra_option = $(thisObj).parent().parent().find(".extra-options");
        var columns = $(thisObj).parent().parent().find(".column_name");
        var select_example_box = $(thisObj).parent().parent().find(".select_example");

        /* integer field */
        if (data_type == "int") {
            select_example_box.val('number');
            extra_option.html('<input type="text" value="0" /> to <input type="text" value="1000" />');
        } else if (data_type == "float") {
            select_example_box.val('number');
            extra_option.html('<input type="text" value="0.00" /> to <input type="text" value="1000.00" />');
        } else if (data_type == "decimal") {
            select_example_box.val('number');
            extra_option.html('<input type="text" value="0.00" /> to <input type="text" value="1000.00" />');
        } else if (data_type == "double") {
            select_example_box.val('number');
            extra_option.html('<input type="text" value="1000" /> to <input type="text" value="100000" />');
        } else if (data_type == "bigint") {
            select_example_box.val('number');
            extra_option.html('<input type="text" value="100000" /> to <input type="text" value="100000000" />');
        } else if (data_type == "tinyint") {
            select_example_box.val('number');
            extra_option.html('<input type="text" value="0" /> to <input type="text" value="1" />');
        }

        /* var char */
        else if (data_type == "varchar") {
            var field = regex_check(columns.text());
            if (field != null) {
                if (field == "name") {
                    extra_option.html("<input type='text' value='f|l' /> f-first, m-middle, l-last name");
                    select_example_box.val(field);
                } else if (field == "phone") {
                    extra_option.html("Prefix<input type='text' value='980,984,986'/> Digits<input type='text' value='10'/> ");
                    select_example_box.val(field);
                } else {
                    select_example_box.val(field);
                    extra_option.html("some dummy " + field);
                }
            } else {
                extra_option.html("Random short text");
                select_example_box.val("short_text");
            }
        }

        /* date field */
        else if (data_type == "date" || data_type == "datetime") {
            select_example_box.val("date");
            extra_option.html('<input type="text" value="' + current_date + ' 00:00:00" /> to <input type="text" value="' + current_date + ' 23:59:59" />');
        }

        else if (data_type == "longtext") {
            extra_option.html("Random long text");
            select_example_box.val("long_text");
        }

        else if (data_type == "enum") {
            extra_option.html('<input type="text" value="" placeholder="Item1 | Item2 | Item3"/>  Enter values separated by | ');
            select_example_box.val("custom_list");
        }

        /* tinytext, text, mediumtext*/
        else if (data_type == "tinytext" || data_type == "text" || data_type == "mediumtext") {
            extra_option.html("Random short text");
            select_example_box.val("short_text");
        }
    }

    function select_example_change(thisObj) {
        var select_type = $(thisObj).val();
        var extra_option = $(thisObj).parent().parent().parent().find(".extra-options");

        /* integer field */
        if (select_type == "name") {
            extra_option.html("<input type='text' value='f|l' /> f-first,m-middle, l-last name");
        } else if (select_type == "phone") {
            extra_option.html("Prefix<input type='text' value='980,984,986'/> Digits<input type='text' value='10'/> ");
        } else if (select_type == "email") {
            extra_option.html("some dummy email");
        } else if (select_type == "date") {
            extra_option.html('<input type="text" value="' + current_date + ' 00:00:00" /> to <input type="text" value="' + current_date + ' 23:59:59" />');
        } else if (select_type == "company") {
            extra_option.html("some dummy company");
        } else if (select_type == "street") {
            extra_option.html("some dummy street");
        } else if (select_type == "short_text") {
            extra_option.html("Random short text");
        } else if (select_type == "long_text") {
            extra_option.html("Random long text");
        } else if (select_type == "number") {
            extra_option.html('<input type="text" value="0" /> to <input type="text" value="1000" />');
        } else if (select_type == "auto_increment") {
            extra_option.html('Auto increment');
        } else if (select_type == "custom_list") {
            extra_option.html('<input type="text" value="" placeholder="Item1 | Item2 | Item3"/>  Enter values separated by | ');
        } else if (select_type == "url") {
            extra_option.html('some dummy url eg: http://dummyurl.com');
        } else if (select_type == "city") {
            extra_option.html('some dummy city Kathmandu, New York ect');
        } else if (select_type == "postal") {
            extra_option.html('random postal code eg: 977, 74, 45');
        } else if (select_type == "region") {
            extra_option.html('random postal code eg: Bagmati, Mechi');
        } else if (select_type == "country") {
            extra_option.html('random country eg: Nepal, USA, UK');
        } else if (select_type == "lat") {
            extra_option.html('random latitude');
        } else if (select_type == "lng") {
            extra_option.html('random longitude');
        } else if (select_type == "username") {
            extra_option.html('random username eg: abc123, bxt456 ');
        }
        else if (select_type == "password") {
            extra_option.html('random password');
        }
    }

    function regex_check(text) {
        var username_regex = /(.*)+(username|userName|user_name|UserName|Username)+(.*)/g;
        var password_regex = /(.*)+(password|Password)+(.*)/g;
        var name_regex = /(.*)+(name|first_name|last_name|full_name|firstname|lastname|middle_name|middlename|name)+(.*)/g;
        var email_regex = /(.*)+(email)+(.*)/;
        var msisdn_regex = /(.*)+(msisdn)+(.*)/g;
        var url_regex = /(^url+(.*))|((.*)url+)/g;

        var example = null;
        if (username_regex.test(text.toLowerCase())) {
            example = "username";
        } else if (password_regex.test(text.toLowerCase())) {
            example = "password";
        } else if (name_regex.test(text.toLowerCase())) {
            example = "name";
        } else if (email_regex.test(text.toLowerCase())) {
            example = "email";
        } else if (msisdn_regex.test(text.toLowerCase())) {
            example = "phone";
        } else if (url_regex.test(text.toLowerCase())) {
            example = "url";
        }
        return example;
    }

    function generate_data(thisObj) {
        if (confirm("Are you sure want to generate?")) {
            send_json("generate",thisObj);
        }
    }
    send_json("preview", $("#btn-preview"));
    function stop_generate() {
        clearInterval(generator_thread);
        $("#btn-preview").attr("disabled",false);
        $("#btn-generate").attr("disabled",false);
        $("#btn-stop").attr("disabled",true);
    }
    var current_speed = 1;
    function send_json(preview_or_generate, thisObj) {
        $(thisObj).attr("disabled",true);
        var table_data = [];
        $(".extra-options").each(function () {
            //debugger;
            var row = {};
            var column = $(this).parent().parent().find(".column_name").text();
            var data_type = $(this).parent().parent().find(".hidden-data-type").val();
            var hidden_column_key = $(this).parent().parent().find(".hidden-column-key").val();
            var selected_data_type = $(this).parent().parent().find(".select_example").val();
            var options = "";
            var pipe = "";
            $(this).find('input').each(function () {
                options += pipe + $(this).val();
                pipe = "|";
            });

            row["column"] = column;
            row["data_type"] = data_type;
            row["hidden_column_key"] = hidden_column_key;
            row["selected_data_type"] = selected_data_type;
            row["options"] = options;
            table_data.push(row);
        });
        var myJsonString = JSON.stringify(table_data);
        var table_name = "<?php echo $table_main?>";
        var database_name = "<?php echo $dbname ?>";
        var speed = 1000;
        var busy = false;
        var batch_size = 100;
        var no_of_rows = parseInt($("#no_of_rows").val());
        var buffer_size = 10;
        if (preview_or_generate == "preview") {
            buffer_size = 10;
            $("#btn-stop").attr("disabled",true);
        }

        if (preview_or_generate == "generate") {
            $("#btn-stop").attr("disabled",false);
            $("#btn-generate").attr("disabled",true);
            $("#btn-preview").attr("disabled",true);
        }

        var processed_data = 0;
        generator_thread = setInterval(function () {
            if (!busy) {
                busy = true;
                $("#generate-speed").text(processed_data + " processed ");

                if (no_of_rows < buffer_size*batch_size) {
                    buffer_size = 1;
                    if(no_of_rows < batch_size) {
                        if (no_of_rows < 1) {
                            stop_generate();
                            return;
                        }
                        batch_size = no_of_rows;
                    }
                }

                var ajax_call = (function () {
                    var json = null;
                    $.ajax({
                        async: false,
                        cache: false,
                        url: "data-generator.php",
                        method: "get",
                        data: {
                            json: myJsonString,
                            no_of_rows: buffer_size,
                            table: table_name,
                            database: database_name,
                            preview_or_generate: preview_or_generate,
                            batch_size:batch_size
                        },
                        success: function (response) {

                            if (preview_or_generate == "preview") {
                                var json_parse = JSON.parse(response);
                                $("#preview-table").find('tbody').html("");
                                json_parse.forEach(function (row) {
                                    var tr = $("<tr/>");

                                    row.forEach(function (col) {
                                        var td = $("<td/>");
                                        td.append("<div class='td-height'>" + col + "</div>");
                                        tr.append(td);
                                    });

                                    $("#preview-table").find('tbody').append(tr);
                                });

                            } else if(preview_or_generate == "generate") {
                                if(response == "error") {
                                    $("#generate-speed").text("SQL error!!, please check data type properly");
                                    stop_generate();
                                }
                            }
                        }
                    });
                    busy = false;
                    processed_data += buffer_size*batch_size;
                    no_of_rows -= buffer_size*batch_size;
                    return json;
                })();
            }

            if (preview_or_generate == "preview") {
                stop_generate()
            }

        }, speed);
    }
</script>
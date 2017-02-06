<form>
    Filter = 
    <select name="filter">
    <option value="murah">
    murah
    </option>
    <option value="sedang">
    sedang
    </option>
    <option value="mahal">
    mahal
    </option>
    <option value="semua">
    semua
    </option>
    </select> 
    <br/>
    Jenis Kelamin = 
    <select name="JK">
    <option value="semua">
    semua
    </option>
    <option value="campur">
    campur
    </option>
    <option value="putra">
    putra
    </option>
    <option value="putri">
    putri
    </option>
    </select> 
    <br/>
    <input type="submit" name="submit" value="ok">

</form>

<?php

$url = "http://localhost/progif/api.php";

$kosan = file_get_contents($url);

$e = json_decode($kosan,true);
echo "KOSAN BANDUNG", "<br/>";
//print_r($e);

$result = array_map(
    function($x) {
        return array(
        "_id"=> $x['_id'], 
        "room-title"=>$x['room-title'],
        "price_title"=>ubah_harga($x['price_title']),
        "available_room"=>$x['available_room'],
        "share_url"=>$x['share_url'],
        "gender"=>ubah_gender($x['gender']));// ['room-title'] ['share_url'];
    }, $e);
print_r($result);
echo json_encode($result);

function ubah_harga($price_title) {
    $harga = explode(" ", $price_title);
    if ($harga[1]=="rb") {
        return $harga[0] * 1000;
    }
    if ($harga[1]=="jt") {
        return $harga[0] * 1000000;
    }
}

function ubah_gender($gender) {
    if ($gender=="0") {
        return "campur";
    }
    if ($gender=="1") {
        return "putra";
    }
    if ($gender=="2") {
        return "putri";
    }
    
}

function filter_harga($filter,$category) {
    if ($category == "semua") {
        return $filter;
    }
    $hasil = array();
    foreach ($filter as $value) {
        if ($category == "murah") {
            if($value['price_title'] <= 500000) {
                $hasil[] = $value;
            }
        }
        else if ($category == "sedang") {
            if(($value['price_title'] > 500000) && ($value['price_title'] <= 1000000)) {
                $hasil[]= $value;
            }
        }
        else if ($category == "mahal") {
            if(($value['price_title'] > 1000000) && ($value['price_title'])) {
                $hasil[]= $value;
            }
        }
    }
    return $hasil;
}

function filter_gender($filter,$category) {
    if ($category == "semua") {
        return $filter;
    }
    $hasil = array();
    foreach ($filter as $value) {
        if ($category == $value['gender']) {
            $hasil[] = $value;
        } 
    }
    return $hasil;
}


function build_table($array){

    $html = '<table border="1">';

    // header row
    $html .= '<tr>';
    foreach($array as $key=>$value){
        $html .= '<tr>';
        foreach($value as $key2=>$value2){
            $html .= '<th>' . $key2 . '</th>';
        }
        $html .= '</tr>';
        break;
    }

    // data rows
    foreach($array as $key=>$value){
        $html .= '<tr>';
        foreach($value as $key2=>$value2){
            $html .= '<td>' . $value2 . '</td>';
        }
        $html .= '</tr>';
    }

    // finish table and return it

    $html .= '</table>';
    return $html;
}


$array = $result;
if (isset($_GET['filter'])) {
    $array = filter_harga($array,$_GET['filter']);
}
if (isset($_GET['JK'])) {
    $array = filter_gender($array,$_GET['JK']);
}
print_r(build_table($array));    

 
?>


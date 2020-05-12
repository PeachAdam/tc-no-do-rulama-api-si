<?php
    session_start();

    $db = mysqli_connect("localhost","root","","tc_sorgulama");
    function tcno_dogrula($bilgiler){
        $gonder = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
            <TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
            <TCKimlikNo>'.$bilgiler["tcno"].'</TCKimlikNo>
            <Ad>'.$bilgiler["isim"].'</Ad>
            <Soyad>'.$bilgiler["soyisim"].'</Soyad>
            <DogumYili>'.$bilgiler["dogumyili"].'</DogumYili>
            </TCKimlikNoDogrula>
            </soap:Body>
            </soap:Envelope>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx" );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_POST,           true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,    $gonder);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
            'POST /Service/KPSPublic.asmx HTTP/1.1',
            'Host: tckimlik.nvi.gov.tr',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
            'Content-Length: '.strlen($gonder)
        ));
        $gelen = curl_exec($ch);
        curl_close($ch);

        return strip_tags($gelen);
    }
    if(isset($_POST['submit'])){
        $isim       =mysqli_real_escape_string($db,$_POST['isim']);
        $soyisim    =mysqli_real_escape_string($db,$_POST['soyisim']);
        $dogumyili  =mysqli_real_escape_string($db,$_POST['dogumyili']);
        $tcno       =mysqli_real_escape_string($db,$_POST['tcno']);
        $email      =mysqli_real_escape_string($db,$_POST['email']);
        $password   =mysqli_real_escape_string($db,$_POST['password']);
        $password2  =mysqli_real_escape_string($db,$_POST['password2']);

        //tc no apisine giden değerler
        $bilgiler = array(
            "isim"      => $isim,
            "soyisim"   => $soyisim,
            "dogumyili" => $dogumyili,
            "tcno"      => $tcno
        );

        //çıkan sonuç sonrasında yapılanlar
        $sonuc = tcno_dogrula($bilgiler);

        if($password == $password2 && $sonuc == "true") {
            //hesap bilgilerini $db ye gidiyor
            $sql = "INSERT INTO tc_sorgulama(isim , soyisim , tcno , dogum_yili , email , password) VALUES('$isim' , '$soyisim' , '$tcno' , '$dogumyili' , '$email' , '$password')";
            mysqli_query($db , $sql);
            //yönlendirmeler
            if($sonuc=="true"){
                header("location: home.php");
            }else{
                echo "Tc no doğrulama başarısız";
            }
        }
        //eğer password ve password2 birbirine eşid değil ise ;
        else{
            echo "Şifreleri doğru giriniz";
        }if ($sonuc != "true"){
            echo "tc no yu doğru giriniz";
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>tc sorgulama</title>
    </head>
    <body>
    <form action="index.php" method="post">
        <h1>hesap oluştur</h1>
        <p>hesap oluşturmak için doldurun</p>
        <hr>
        <label><b>isim:</b></label>
        <input type="text" placeholder="isim adı giriniz" class="textInput" name="isim">

        <label><b>soyisim:</b></label>
        <input type="text" placeholder="soyisim adı giriniz" class="textInput" name="soyisim" >

        <label><b>dogumyili:</b></label>
        <input type="text" placeholder="doğum yılı giriniz" class="textInput" name="dogumyili">

        <label><b>tc kimlik no:</b></label>
        <input type="text" placeholder="tc no giriniz" class="textInput" name="tcno">

        <label><b>Email:</b></label>
        <input type="text" placeholder="Email giriniz" class="textInput" name="email" >

        <label><b>Sifre:</b></label>
        <input type="password" placeholder="Sifre giriniz " class="textInput" name="password" >

        <label><b>Sifre dogrulama:</b></label>
        <input type="password" placeholder="Sifre dogrulama" class="textInput" name="password2" >
        <hr>
        <button class="registerbtn"  type="submit" name="submit">Olustur</button>
    </form>
    </body>
</html>




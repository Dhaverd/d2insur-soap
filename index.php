<?php

function soap_request($requestData)
{
    $wsdlUrl = 'https://soapdev.d2insur.ru/pay/PolicyPay.wsdl';
    $login = 'testForUser';
    $password = 'testUser520';

    try {
        $client = new SoapClient($wsdlUrl, array(
            'login' => $login,
            'password' => $password,
            'soap_version' => SOAP_1_1,
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                    ]
                ]
            )
        ));
        return $client->obtainCertificate($requestData);
    } catch (SoapFault $fault) {
        $response = new stdClass();
        $response->result = new stdClass();
        $response->result->code = 'SOAP_FAULT';
        $response->result->errorDescr = $fault->getMessage();
        return $response;
    }
}

function base64_to_rdf($response)
{
    $pdfData = $response->cert->certFile;
    $pdfContent = base64_decode($pdfData);
    $filePath = './certificate.pdf';
    file_put_contents($filePath, $pdfContent);

    echo "PDF файл успешно сохранен по адресу: $filePath";
}

$INSURER_FIRSTNAME = 'Иван';
$INSURER_LASTNAME = 'Иванович';
$INSURER_SURNAME = 'Иванов';
$INSURER_EMAIL = 'ivanov@example.com';
$INSURER_BIRTHDAY = '01.01.1980';
$PASSPORT_NUMBER = '5747 373636';
$INSURER_PHONE = '79991234567';

$applicationId = '12345678';
$productId = '3523309775';

$requestData = array(
    'applicationId' => $applicationId,
    'productId' => $productId,
    'person' => array(
        'INSURER_FIRSTNAME' => $INSURER_FIRSTNAME,
        'INSURER_LASTNAME' => $INSURER_LASTNAME,
        'INSURER_SURNAME' => $INSURER_SURNAME,
        'INSURER_EMAIL' => $INSURER_EMAIL,
        'INSURER_BIRTHDAY' => $INSURER_BIRTHDAY,
        'PASSPORT_NUMBER' => $PASSPORT_NUMBER,
        'INSURER_PHONE' => $INSURER_PHONE
    )
);

$soap_response = soap_request($requestData);
if ($soap_response->result->code === 'OK') {
    base64_to_rdf($soap_response);
} else {
    print_r($soap_response);
}


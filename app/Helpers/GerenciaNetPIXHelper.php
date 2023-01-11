<?php
namespace App\Helpers;

class GerenciaNetPIXHelper {
    private static $client_id = 'Client_Id_196f217841a5cae04eb2d01c9041484bf63fe41e';
    private static $client_secret = 'Client_Secret_9e3e28fe58b4dffeffb665c59716445eb65f0f11';
    private static $certificate_path = '/etc/gerencianet/goiasbetclub/producao-429971-goiasbetclub.pem';

    private static function OAuthToken() {
        $config = [
            "certificate" => self::$certificate_path,
            "client_id" => self::$client_id,
            "client_secret" => self::$client_secret,
          ];

        $authorization =  base64_encode($config["client_id"] . ":" . $config["client_secret"]);

        try {
            $curl = curl_init();

            if ($curl === false) {
                throw new Exception('failed to initialize');
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api-pix.gerencianet.com.br/oauth/token", // Rota base, homologação ou produção
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '{"grant_type": "client_credentials"}',
                CURLOPT_SSLCERT => self::$certificate_path, // Caminho do certificado
                CURLOPT_SSLCERTPASSWD => "",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic $authorization",
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            if ($response === false) {
                throw new \Exception(curl_error($curl), curl_errno($curl));
            }

            $response = json_decode($response);
            return $response->access_token;

            $httpReturnCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        } finally {
            if (is_resource($curl)) {
                curl_close($curl);
            }
        }
    }

    private static function PixEvpKey() {
        $config = [
            "certificate" => self::$certificate_path,
            "client_id" => self::$client_id,
            "client_secret" => self::$client_secret,
          ];

        $authorization =  base64_encode($config["client_id"] . ":" . $config["client_secret"]);

        try {
            $curl = curl_init();

            if ($curl === false) {
                throw new Exception('failed to initialize');
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api-pix.gerencianet.com.br/v2/gn/evp", // Rota base, homologação ou produção
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => '',
                CURLOPT_SSLCERT => self::$certificate_path, // Caminho do certificado
                CURLOPT_SSLCERTPASSWD => "",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer ".GerenciaNetPIXHelper::OAuthToken(),
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            if ($response === false) {
                throw new \Exception(curl_error($curl), curl_errno($curl));
            }

            $response = json_decode($response);
            dd($response);

            $httpReturnCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        } finally {
            if (is_resource($curl)) {
                curl_close($curl);
            }
        }
    }

    private static function PixWebhookConfig() {
        $config = [
            "certificate" => self::$certificate_path,
            "client_id" => self::$client_id,
            "client_secret" => self::$client_secret,
          ];

        $authorization =  base64_encode($config["client_id"] . ":" . $config["client_secret"]);

        try {
            $curl = curl_init();

            if ($curl === false) {
                throw new Exception('failed to initialize');
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api-pix.gerencianet.com.br/v2/webhook/326aec75-9bd2-40ac-8568-047bf2769827", // Rota base, homologação ou produção
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS => '{"webhookUrl": "https://www.goiasbetclub.com/api/paymentconfirm"}',
                CURLOPT_SSLCERT => self::$certificate_path, // Caminho do certificado
                CURLOPT_SSLCERTPASSWD => "",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer ".GerenciaNetPIXHelper::OAuthToken(),
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            if ($response === false) {
                throw new \Exception(curl_error($curl), curl_errno($curl));
            }

            $response = json_decode($response);
            dd($response);

            $httpReturnCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        } finally {
            if (is_resource($curl)) {
                curl_close($curl);
            }
        }
    }
    
    public static function imediateChargeTxid($price, $card_code) {
        $config = [
            "certificate" => self::$certificate_path,
            "client_id" => self::$client_id,
            "client_secret" => self::$client_secret,
          ];

        $authorization =  base64_encode($config["client_id"] . ":" . $config["client_secret"]);
        
        $body = [
            'calendario' => ['expiracao' => 2147483647],
            'valor' => ['original' => '00.01'],
            'chave' => '326aec75-9bd2-40ac-8568-047bf2769827',
            'solicitacaoPagador' => "www.goiasbetclub.com - cartela: $card_code",
        ];

        try {
            $curl = curl_init();

            if ($curl === false) {
                throw new Exception('failed to initialize');
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api-pix.gerencianet.com.br/v2/cob/".md5(time().rand(0, 9999)), 
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_SSLCERT => self::$certificate_path, // Caminho do certificado
                CURLOPT_SSLCERTPASSWD => "",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer ".GerenciaNetPIXHelper::OAuthToken(),
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            if ($response === false) {
                throw new \Exception(curl_error($curl), curl_errno($curl));
            }

            $response = json_decode($response);
            dd($response);

            $httpReturnCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        } finally {
            if (is_resource($curl)) {
                curl_close($curl);
            }
        }
    }

    public static function test() {
        dd(GerenciaNetPIXHelper::PixWebhookConfig());
    }
}

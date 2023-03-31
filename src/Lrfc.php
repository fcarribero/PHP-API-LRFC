<?php


namespace Advans\Api\Lrfc;

class Lrfc {

    protected Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function version(): string {
        return $this->call('version');
    }

    public function getByRFC($rfc, $fecha = null) {
        if ($fecha === null) {
            $fecha = date('Y-m-d');
        } else {
            $fecha = date('Y-m-d', strtotime($fecha));
        }
        $valid_rfc = preg_match('/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $rfc);
        if (!$valid_rfc) {
            throw new LrfcException('El RFC no es válido');
        }
        try {
            return $this->call("consultar/by-rfc/{$fecha}/{$rfc}");
        } catch (LrfcException $e) {
            if ($e->getCode() == 404) {
                return null;
            }
            throw $e;
        }
    }

    public function getByNombre($nombre, $fecha = null) {
        if ($fecha === null) {
            $fecha = date('Y-m-d');
        } else {
            $fecha = date('Y-m-d', strtotime($fecha));
        }
        try {
            return $this->call("consultar/by-nombre/{$fecha}/{$nombre}");
        } catch (LrfcException $e) {
            if ($e->getCode() == 404) {
                return null;
            }
            throw $e;
        }
    }

    public function getByCodigoPostal($cp, $fecha = null) {
        if ($fecha === null) {
            $fecha = date('Y-m-d');
        } else {
            $fecha = date('Y-m-d', strtotime($fecha));
        }
        $valid_cp = preg_match('/^[0-9]{5}$/', $cp);
        if (!$valid_cp) {
            throw new LrfcException('El código postal no es válido');
        }
        try {
            return $this->call("consultar/by-cp/{$fecha}/{$cp}");
        } catch (LrfcException $e) {
            if ($e->getCode() == 404) {
                return null;
            }
            throw $e;
        }
    }

    protected function call($method, $verb = 'GET', $params = null): string {
        $verb = strtoupper($verb);
        $url = $this->config->base_url . $method;
        $curl = curl_init();
        $postfields = null;
        if ($verb == 'POST') {
            $postfields = gettype($params) == 'array' ? json_encode($params) : $params;
        }
        curl_setopt_array($curl, [
            CURLOPT_URL => $url . ($verb == 'GET' && $params ? '?' . http_build_query($params) : ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $verb,
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->config->key
            ],
        ]);
        if (!($result = @curl_exec($curl))) {
            throw new LrfcException('Error de conexión');
        }
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($http_code != 200 || $result === false) {
            throw new LrfcException('El servicio regresó un código de error ' . $http_code . ' ' . $result, $http_code);
        }
        return $result;
    }
}
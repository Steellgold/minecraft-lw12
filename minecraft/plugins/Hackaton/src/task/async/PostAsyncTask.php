<?php

namespace hackaton\task\async;

use Closure;

class PostAsyncTask extends RequestAsync {

    /** @var string */
    private string $data;

    /**
     * @param string $url
     * @param array $data
     * @param Closure $callback
     */
    public function __construct(private readonly string $url, array $data, Closure $callback) {
        $this->data = json_encode($data);
        parent::__construct();
        RequestPool::add($this->getId(), $callback);
    }

    /**
     * @return void
     */
    public function onRun(): void {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->getUrl() . $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->data,
            CURLOPT_HTTPHEADER => [
                "x-api-key: " . $this->getApiKey(),
                "Content-Type: application/json",
                "Accept: application/json"
            ],
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ]);

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (isset($response["error"]) || str_contains($response, "error")) {
            $response = json_decode($response, true);
            curl_close($curl);

            $this->setResult(new RequestError(($response["message"] ?? ""), $code));
            return;
        }

        if (curl_errno($curl)) {
            curl_close($curl);

            $this->setResult(new RequestError(curl_error($curl), curl_errno($curl)));
            return;
        }

        curl_close($curl);

        $this->setResult(json_decode($response, true));
    }
}
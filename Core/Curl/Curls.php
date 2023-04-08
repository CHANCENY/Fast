<?php

namespace Curls;

class Curls
{
   private $headers = [];

   private $resultBody;

    /**
     * @return mixed
     */
    public function getResultBody()
    {
        return $this->resultBody;
    }

    /**
     * @param mixed $resultBody
     */
    public function setResultBody($resultBody): void
    {
        $this->resultBody = $resultBody;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param mixed $baseUrl
     */
    public function setBaseUrl($baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }
   private $baseUrl;

    private $url;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }



    public function addHeader($key , $value){
        array_push($this->headers, $key.': '.$value);
    }

    public function runCurl($endpoint = ""){
        $curl = curl_init( !empty($this->getUrl()) ? $this->getUrl() : $this->baseUrl.'/'.$endpoint);

        if(!empty($this->headers)){
           curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        if(!empty($result)){
          $this->resultBody = json_decode($result, true);
        }else{
            $this->resultBody = [];
        }
        curl_close($curl);
    }


}
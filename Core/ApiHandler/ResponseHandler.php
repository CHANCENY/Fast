<?php
/**
 * this file has class ResponseHandler that send api responses
 * to front-end callers
 */

namespace ResponseHandler;
class ResponseHandler{

    /**
     * class whose response need to be sent
     */
    private $objectClasses;

    /**
     * this is payload to be sent as result of reqult
     */
    private $payloadResponse = [];

     /**
      * status code to be sent
      */
    private $statusCode;


     /**
     * setter for class object
     */
    public function setClassObject($class){
       $this->objectClasses =  $class;
       $this->setPayload($this->objectClasses->getResult());
    }

    /**
     * setter for payload
     */
    public function setPayload($payload){
        if(empty($this->payloadResponse)){
            $this->payloadResponse = $payload;
        }else{
           $this->payloadResponse = array_merge($this->payloadResponse, $payload);
        }
    }

    /**
     * content type value to be for data payload
     */
    public function setAllowContentType($contenttype){
        switch($contenttype){
            case 'application/json':
                $this->payloadResponse = json_encode($this->payloadResponse);
                break;
        }
    }

    /**
     * handles http status
     */
    public function headerCodes($statusCode = 0){

        switch($statusCode){
            case 100:
                $this->statusCode = 100;
                $this->setPayload([
                    'status'=>100,
                    'message'=>'This interim response indicates that the client should continue the request or ignore the response if the request is already finished.'
                ]);
                break;
            case 101:
                $this->statusCode = 101;
                $this->setPayload([
                    'status'=>101,
                    'message'=>'This code is sent in response to an Upgrade request header from the client and indicates the protocol the server is switching to'
                ]);
                break;
            case 103:
                $this->statusCode = 103;
                $this->setPayload([
                    'status'=>103,
                    'message'=>'This status code is primarily intended to be used with the Link header, letting the user agent start preloading resources while the server prepares a response.'
                ]);
                break;
            case 200:
                $this->statusCode = 200;
                break;
            case 201:
                $this->statusCode = 201;
                $this->setPayload([
                    'status'=>201,
                    'message'=>'This status code is primarily intended to be used with the Link header, letting the user agent start preloading resources while the server prepares a response.'
                ]);
                break;
            case 202:
                $this->statusCode = 202;
                $this->setPayload([
                    'status'=>202,
                    'message'=>'The request has been received but not yet acted upon. It is noncommittal, since there is no way in HTTP to later send an asynchronous response indicating the outcome of the request. It is intended for cases where another process or server handles the request, or for batch processing'
                ]);
                break;
            case 203:
                $this->statusCode = 203;
                $this->setPayload([
                    'status'=>203,
                    'message'=>'This response code means the returned metadata is not exactly the same as is available from the origin server, but is collected from a local or a third-party copy. This is mostly used for mirrors or backups of another resource. Except for that specific case, the 200 OK response is preferred to this status.'
                ]);
                break;
            case 204:
                $this->statusCode = 204;
                $this->setPayload([
                    'status'=>204,
                    'message'=>'There is no content to send for this request, but the headers may be useful. The user agent may update its cached headers for this resource with the new ones.'
                ]);
                break;
            case 205:
                $this->statusCode = 205;
                $this->setPayload([
                    'status'=>205,
                    'message'=>'Tells the user agent to reset the document which sent this request'
                ]);
                break;
            case 206:
                $this->statusCode = 206;
                $this->setPayload([
                    'status'=>206,
                    'message'=>'This response code is used when the Range header is sent from the client to request only part of a resource.

                    '
                ]);
                break;
            case 400:
                $this->statusCode = 400;
                $this->setPayload([
                    'status'=>400,
                    'message'=>'The server cannot or will not process the request due to something that is perceived to be a client error (e.g., malformed request syntax, invalid request message framing, or deceptive request routing).
                    '
                ]);
                break;
            case 401:
                $this->statusCode = 401;
                $this->setPayload([
                    'status'=>401,
                    'message'=>'Although the HTTP standard specifies "unauthorized", semantically this response means "unauthenticated". That is, the client must authenticate itself to get the requested response.
                    '
                ]);
                break;
            case 402:
                $this->statusCode = 402;
                $this->setPayload([
                    'status'=>402,
                    'message'=>'This response code is reserved for future use. The initial aim for creating this code was using it for digital payment systems, however this status code is used very rarely and no standard convention exists.
                    '
                ]);
                break;
            case 403:
                $this->statusCode = 403;
                $this->setPayload([
                    'status'=>403,
                    'message'=>'The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource. Unlike 401 Unauthorized, the clients identity is known to the server.
                    '
                ]);
                break;
            case 404:
                $this->statusCode = 404;
                $this->setPayload([
                    'status'=>404,
                    'message'=>'The server cannot find the requested resource. In the browser, this means the URL is not recognized. In an API, this can also mean that the endpoint is valid but the resource itself does not exist. Servers may also send this response instead of 403 Forbidden to hide the existence of a resource from an unauthorized client. This response code is probably the most well known due to its frequent occurrence on the web
                    '
                ]);
                break;
            case 405:
                $this->statusCode = 405;
                $this->setPayload([
                    'status'=>405,
                    'message'=>'The request method is known by the server but is not supported by the target resource.
                    '
                ]);
                break;
            case 406:
                $this->statusCode = 406;
                $this->setPayload([
                    'status'=>406,
                    'message'=>'This response is sent when the web server, after performing server-driven content negotiation, doesnt find any content that conforms to the criteria given by the user agent.
                    '
                ]);
                break;
            case 408:
                $this->statusCode = 408;
                $this->setPayload([
                    'status'=>408,
                    'message'=>'This response is sent on an idle connection by some servers, even without any previous request by the client. It means that the server would like to shut down this unused connection. This response is used much more since some browsers, like Chrome, Firefox 27+, or IE9, use HTTP pre-connection mechanisms to speed up surfing. Also note that some servers merely shut down the connection without sending this messag
                    '
                ]);
                break;
            case 409:
                $this->statusCode = 409;
                $this->setPayload([
                    'status'=>409,
                    'message'=>'This response is sent when a request conflicts with the current state of the server.
                    '
                ]);
                break;
            default:
               $this->statusCode = 500;
        }

    }

    /**
     * sender responses
     */
    public function send($contenttype = ""){
        http_response_code($this->statusCode);
        echo $this->payloadResponse;
    }
}

?>
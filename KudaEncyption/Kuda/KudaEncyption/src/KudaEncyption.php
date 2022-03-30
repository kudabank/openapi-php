<?php

namespace Kuda\KudaEncyption;

use Crypt_RSA;
use Crypt_Rijndael;


class KudaEncyption
{

	public function __construct()
	{
	
	}
    public function RSAEncrypt($data, $publicKey)
	{
        $rsa = new Crypt_RSA();

        $rsa->loadKey($publicKey);

        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

        $encryptedData = $rsa->encrypt($data);

        $encodedData = base64_encode($encryptedData);
        return $encodedData;

    }

    public function RSADecrypt($data, $privateKey)
	{
        $rsa = new Crypt_RSA();
        $rsa->loadKey($privateKey);
        
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $decodedData = base64_decode($data);
        $decryptedData = $rsa->decrypt($decodedData);
        return $decryptedData;

    }
	
	public function AESEncrypt($data, $password)
	{
		$cipher = new Crypt_Rijndael();
		$cipher->setPassword($password, 'pbkdf2', 'sha1', 'randomsalt', 1000, 256 / 8);
		$encryptedData = $cipher->encrypt($data);
		$encodedData = base64_encode($encryptedData);
		return $encodedData;
	}
	
	public function AESDecrypt($data, $password)
	{
		$cipher = new Crypt_Rijndael();
		$decodedData = base64_decode($data);
		$cipher->setPassword($password, 'pbkdf2', 'sha1', 'randomsalt', 1000, 256 / 8);
		$decryptedData = $cipher->decrypt($decodedData);
		return $decryptedData;
	}
}

<?php

/**
 * AES_CRYPTO Administration Class
 * This class represents AES Rijndael 128 / 256 Administration Class
 * with the Hybernate framework 
 * AES Cipher function: encrypt 'input' with Rijndael algorithm
 *
 * @package		CLASSES::MAIL::MAIL
 * @subpackage	none
 * @author      Avi Aialon <aviaialon@gmail.com>
 * @copyright	2010 Deviant Logic. All Rights Reserved
 * @license		http://www.deviantlogic.ca/license
 * @version		SVN: $Id$
 * @link		SVN: $HeadURL$
 * @since		12:35:53 PM
 *
 * REQUIRES MCRYPT LIB: http://php.net/manual/en/mcrypt.installation.php	
 */	 
// Sbox is pre-computed multiplicative inverse in GF(2^8) used in SubBytes and KeyExpansion [5.1.1]
$Sbox = array (
	0x63, 0x7c, 0x77, 0x7b, 
	0xf2, 0x6b,	0x6f, 0xc5, 
	0x30, 0x01,	0x67, 0x2b,
	0xfe, 0xd7,	0xab, 0x76, 
	0xca, 0x82,	0xc9, 0x7d, 
	0xfa, 0x59, 0x47, 0xf0, 
	0xad, 0xd4, 0xa2, 0xaf, 
	0x9c, 0xa4, 0x72, 0xc0, 
	0xb7, 0xfd, 0x93, 0x26, 
	0x36, 0x3f, 0xf7, 0xcc, 
	0x34, 0xa5, 0xe5, 0xf1, 
	0x71, 0xd8, 0x31, 0x15, 
	0x04, 0xc7, 0x23, 0xc3, 
	0x18, 0x96, 0x05, 0x9a, 
	0x07, 0x12, 0x80, 0xe2, 
	0xeb, 0x27, 0xb2, 0x75, 
	0x09, 0x83, 0x2c, 0x1a, 
	0x1b, 0x6e, 0x5a, 0xa0, 
	0x52, 0x3b, 0xd6, 0xb3, 
	0x29, 0xe3, 0x2f, 0x84, 
	0x53, 0xd1, 0x00, 0xed, 
	0x20, 0xfc, 0xb1, 0x5b, 
	0x6a, 0xcb, 0xbe, 0x39, 
	0x4a, 0x4c, 0x58, 0xcf, 
	0xd0, 0xef, 0xaa, 0xfb, 
	0x43, 0x4d, 0x33, 0x85, 
	0x45, 0xf9, 0x02, 0x7f, 
	0x50, 0x3c, 0x9f, 0xa8, 
	0x51, 0xa3, 0x40, 0x8f, 
	0x92, 0x9d, 0x38, 0xf5, 
	0xbc, 0xb6, 0xda, 0x21, 
	0x10, 0xff, 0xf3, 0xd2, 
	0xcd, 0x0c, 0x13, 0xec, 
	0x5f, 0x97, 0x44, 0x17, 
	0xc4, 0xa7, 0x7e, 0x3d, 
	0x64, 0x5d, 0x19, 0x73, 
	0x60, 0x81, 0x4f, 0xdc, 
	0x22, 0x2a, 0x90, 0x88, 
	0x46, 0xee, 0xb8, 0x14, 
	0xde, 0x5e, 0x0b, 0xdb, 
	0xe0, 0x32, 0x3a, 0x0a, 
	0x49, 0x06, 0x24, 0x5c, 
	0xc2, 0xd3, 0xac, 0x62, 
	0x91, 0x95, 0xe4, 0x79, 
	0xe7, 0xc8, 0x37, 0x6d, 
	0x8d, 0xd5, 0x4e, 0xa9, 
	0x6c, 0x56, 0xf4, 0xea, 
	0x65, 0x7a, 0xae, 0x08, 
	0xba, 0x78, 0x25, 0x2e, 
	0x1c, 0xa6, 0xb4, 0xc6, 
	0xe8, 0xdd, 0x74, 0x1f, 
	0x4b, 0xbd, 0x8b, 0x8a, 
	0x70, 0x3e, 0xb5, 0x66, 
	0x48, 0x03, 0xf6, 0x0e, 
	0x61, 0x35, 0x57, 0xb9, 
	0x86, 0xc1, 0x1d, 0x9e, 
	0xe1, 0xf8, 0x98, 0x11, 
	0x69, 0xd9, 0x8e, 0x94, 
	0x9b, 0x1e, 0x87, 0xe9, 
	0xce, 0x55, 0x28, 0xdf, 
	0x8c, 0xa1, 0x89, 0x0d, 
	0xbf, 0xe6, 0x42, 0x68, 
	0x41, 0x99, 0x2d, 0x0f, 
	0xb0, 0x54, 0xbb, 0x16 
);

// Rcon is Round Constant used for the Key Expansion [1st col is 2^(r-1) in GF(2^8)] [5.2]
$Rcon = array (
	array (0x00, 0x00, 0x00, 0x00 ), 
	array (0x01, 0x00, 0x00, 0x00 ), 
	array (0x02, 0x00, 0x00, 0x00 ), 
	array (0x04, 0x00, 0x00, 0x00 ), 
	array (0x08, 0x00, 0x00, 0x00 ), 
	array (0x10, 0x00, 0x00, 0x00 ), 
	array (0x20, 0x00, 0x00, 0x00 ), 
	array (0x40, 0x00, 0x00, 0x00 ), 
	array (0x80, 0x00, 0x00, 0x00 ), 
	array (0x1b, 0x00, 0x00, 0x00 ), 
	array (0x36, 0x00, 0x00, 0x00 ) 
);

class AES_CRYPTO extends Exception {
	protected $strEncryptionKey 	= NULL; // Encryption private key
	protected $intEncryptionBits	= 256;  // Encryption bits [128|256]
	
	const AES_CRYPTO_ENCRYPT_BITS_128 = 128;
	const AES_CRYPTO_ENCRYPT_BITS_256 = 256;
	
	/**
	 * Returns a class instance
	 */
	public static final function getInstance()
	{
		$objCrypto = new AES_CRYPTO();	
		$objCrypto->setEncryptionKey(__ENCRYPTION_KEY__);
		$objCrypto->setEncryptionBits(AES_CRYPTO::AES_CRYPTO_ENCRYPT_BITS_256);
		return ($objCrypto);
	}
	
	/**
	 * Class Constructor
	 * 
	 */
	public function __construct() {
		
	}
	
	/**
	 * Class Destructor 
	 *
	 */
	public function __destruct() { }
	
	/**
	 * Entry Method
	 * Encrypts the string
	 * @param String - The string to encrypt
	 * @return void
	 */
	public final function encrypt($strText = NULL)
	{
		return ($this->AESEncryptCtr(
			$strText, 
			$this->getEncryptionKey(), 
			$this->getEncryptionBits()
		));	
	}
	
	/**
	 * Entry Method
	 * Decrypts the string
	 * @param String - The string to encrypt
	 * @return void
	 */
	public final function decrypt($strText = NULL)
	{
		return ($this->AESDecryptCtr(
			$strText, 
			$this->getEncryptionKey(), 
			$this->getEncryptionBits()
		));	
	}
	
	/**
	 * Sets the encryption key to use
	 * @param String - The encryption key to use
	 * @return void
	 */
	 public function setEncryptionKey($strKey = NULL)
	 {
		$this->strEncryptionKey = $strKey;
	 }
	 
	/**
	 * Gets the encryption key to use
	 * @param none
	 * @return String - The encrytion key
	 */
	 private function getEncryptionKey()
	 {
		return ($this->strEncryptionKey);
	 }
	 
	 /**
	 * Sets the encryption bits algorithim to use
	 * @param Integer - The encryption bits algorithim to use - Default: AES_CRYPTO::AES_CRYPTO_ENCRYPT_BITS_128
	 * @return vois
	 */
	 public function setEncryptionBits($intBits = AES_CRYPTO::AES_CRYPTO_ENCRYPT_BITS_128)
	 {
		$this->intEncryptionBits = (int) $intBits;
	 }
	 
	/**
	 * Gets the encryption bits algorithim to use
	 * @param none
	 * @return Integer - The encrytion bits to use
	 */
	 private function getEncryptionBits()
	 {
		return ((int) $this->intEncryptionBits);
	 }
	
	/**
	 * AES Cipher function: encrypt 'input' with Rijndael algorithm
	 *
	 * @param input message as byte-array (16 bytes)
	 * @param w     key schedule as 2D byte-array (Nr+1 x Nb bytes) -  
	 * 				generated from the cipher key by KeyExpansion()
	 * @return      ciphertext as byte-array (16 bytes)
	 */
	protected final function Cipher($input, $w) { // main Cipher function [5.1]
		$Nb = 4; // block size (in words): no of columns in state (fixed at 4 for AES)
		$Nr = count ( $w ) / $Nb - 1; // no of rounds: 10/12/14 for 128/192/256-bit keys
		
	
		$state = array (); // initialise 4xNb byte-array 'state' with input [3.4]
		for($i = 0; $i < 4 * $Nb; $i ++)
			$state [$i % 4] [floor ( $i / 4 )] = $input [$i];
		
		$state = $this->AddRoundKey ( $state, $w, 0, $Nb );
		
		for($round = 1; $round < $Nr; $round ++) { // apply Nr rounds
			$state = $this->SubBytes ( $state, $Nb );
			$state = $this->ShiftRows ( $state, $Nb );
			$state = $this->MixColumns ( $state, $Nb );
			$state = $this->AddRoundKey ( $state, $w, $round, $Nb );
		}
		
		$state = $this->SubBytes ( $state, $Nb );
		$state = $this->ShiftRows ( $state, $Nb );
		$state = $this->AddRoundKey ( $state, $w, $Nr, $Nb );
		
		$output = array (4 * $Nb ); // convert state to 1-d array before returning [3.4]
		for($i = 0; $i < 4 * $Nb; $i ++)
			$output [$i] = $state [$i % 4] [floor ( $i / 4 )];
		return $output;
	}
	

	protected final function AddRoundKey($state, $w, $rnd, $Nb) { // xor Round Key into state S [5.1.4]
		for($r = 0; $r < 4; $r ++) {
			for($c = 0; $c < $Nb; $c ++)
				$state [$r] [$c] ^= $w [$rnd * 4 + $c] [$r];
		}
		return $state;
	}
	
	protected final function SubBytes($s, $Nb) { // apply SBox to state S [5.1.1]
		global $Sbox; // PHP needs explicit declaration to access global variables!
		for($r = 0; $r < 4; $r ++) {
			for($c = 0; $c < $Nb; $c ++)
				$s [$r] [$c] = $Sbox [$s [$r] [$c]];
		}
		return $s;
	}
	
	protected final function ShiftRows($s, $Nb) { // shift row r of state S left by r bytes [5.1.2]
		$t = array (4 );
		for($r = 1; $r < 4; $r ++) {
			for($c = 0; $c < 4; $c ++)
				$t [$c] = $s [$r] [($c + $r) % $Nb]; // shift into temp copy
			for($c = 0; $c < 4; $c ++)
				$s [$r] [$c] = $t [$c]; // and copy back
		} // note that this will work for Nb=4,5,6, but not 7,8 (always 4 for AES):
		return $s; // see fp.gladman.plus.com/cryptography_technology/rijndael/aes.spec.311.pdf 
	}
	
	protected final function MixColumns($s, $Nb) { // combine bytes of each col of state S [5.1.3]
		for($c = 0; $c < 4; $c ++) {
			$a = array (4 ); // 'a' is a copy of the current column from 's'
			$b = array (4 ); // 'b' is a?{02} in GF(2^8)
			for($i = 0; $i < 4; $i ++) {
				$a [$i] = $s [$i] [$c];
				$b [$i] = $s [$i] [$c] & 0x80 ? $s [$i] [$c] << 1 ^ 0x011b : $s [$i] [$c] << 1;
			}
			// a[n] ^ b[n] is a?{03} in GF(2^8)
			$s [0] [$c] = $b [0] ^ $a [1] ^ $b [1] ^ $a [2] ^ $a [3]; // 2*a0 + 3*a1 + a2 + a3
			$s [1] [$c] = $a [0] ^ $b [1] ^ $a [2] ^ $b [2] ^ $a [3]; // a0 * 2*a1 + 3*a2 + a3
			$s [2] [$c] = $a [0] ^ $a [1] ^ $b [2] ^ $a [3] ^ $b [3]; // a0 + a1 + 2*a2 + 3*a3
			$s [3] [$c] = $a [0] ^ $b [0] ^ $a [1] ^ $a [2] ^ $b [3]; // 3*a0 + a1 + a2 + 2*a3
		}
		return $s;
	}
	
	

	/**
	 * Key expansion for Rijndael Cipher(): performs key expansion on cipher key
	 * to generate a key schedule
	 *
	 * @param key cipher key byte-array (16 bytes)
	 * @return    key schedule as 2D byte-array (Nr+1 x Nb bytes)
	 */
	protected final function KeyExpansion($key) { // generate Key Schedule from Cipher Key [5.2]
		global $Rcon; // PHP needs explicit declaration to access global variables!
		$Nb = 4; // block size (in words): no of columns in state (fixed at 4 for AES)
		$Nk = count ( $key ) / 4; // key length (in words): 4/6/8 for 128/192/256-bit keys
		$Nr = $Nk + 6; // no of rounds: 10/12/14 for 128/192/256-bit keys
		
	
		$w = array ();
		$temp = array ();
		
		for($i = 0; $i < $Nk; $i ++) {
			$r = array ($key [4 * $i], $key [4 * $i + 1], $key [4 * $i + 2], $key [4 * $i + 3] );
			$w [$i] = $r;
		}
		
		for($i = $Nk; $i < ($Nb * ($Nr + 1)); $i ++) {
			$w [$i] = array ();
			for($t = 0; $t < 4; $t ++)
				$temp [$t] = $w [$i - 1] [$t];
			if ($i % $Nk == 0) {
				$temp = $this->SubWord ( $this->RotWord ( $temp ) );
				for($t = 0; $t < 4; $t ++)
					$temp [$t] ^= $Rcon [$i / $Nk] [$t];
			} else if ($Nk > 6 && $i % $Nk == 4) {
				$temp = $this->SubWord ( $temp );
			}
			for($t = 0; $t < 4; $t ++)
				$w [$i] [$t] = $w [$i - $Nk] [$t] ^ $temp [$t];
		}
		return $w;
	}
	
	protected final function SubWord($w) { // apply SBox to 4-byte word w
		global $Sbox; // PHP needs explicit declaration to access global variables!
		for($i = 0; $i < 4; $i ++)
			$w [$i] = $Sbox [$w [$i]];
		return $w;
	}
	
	protected final function RotWord($w) { // rotate 4-byte word w left by one byte
		$tmp = $w [0];
		for($i = 0; $i < 3; $i ++)
			$w [$i] = $w [$i + 1];
		$w [3] = $tmp;
		return $w;
	}
	
	
	
	
	/** 
	 * Encrypt a text using AES encryption in Counter mode of operation
	 * - see http://csrc.nist.gov/publications/nistpubs/800-38a/sp800-38a.pdf
	 *
	 * Unicode multi-byte character safe
	 *
	 * @param plaintext source text to be encrypted
	 * @param password  the password to use to generate a key
	 * @param nBits     number of bits to be used in the key (128, 192, or 256)
	 * @return          encrypted text
	 */
	protected final function AESEncryptCtr($plaintext, $password, $nBits) {
		$blockSize = 16; // block size fixed at 16 bytes / 128 bits (Nb=4) for AES
		if (! ($nBits == 128 || $nBits == 192 || $nBits == 256))
			return ''; // standard allows 128/192/256 bit keys
		// note PHP (5) gives us plaintext and password in UTF8 encoding!
		
	
		// use AES itself to encrypt password to get cipher key (using plain password as source for  
		// key expansion) - gives us well encrypted key
		$nBytes = $nBits / 8; // no bytes in key
		$pwBytes = array ();
		for($i = 0; $i < $nBytes; $i ++)
			$pwBytes [$i] = ord ( substr ( $password, $i, 1 ) ) & 0xff;
		$key = $this->Cipher ( $pwBytes, $this->KeyExpansion ( $pwBytes ) );
		$key = array_merge ( $key, array_slice ( $key, 0, $nBytes - 16 ) ); // expand key to 16/24/32 bytes long 
		
	
		// initialise counter block (NIST SP800-38A B.2): millisecond time-stamp for nonce in 
		// 1st 8 bytes, block counter in 2nd 8 bytes
		$counterBlock = array ();
		$nonce = floor ( microtime ( true ) * 1000 ); // timestamp: milliseconds since 1-Jan-1970
		$nonceSec = floor ( $nonce / 1000 );
		$nonceMs = $nonce % 1000;
		// encode nonce with seconds in 1st 4 bytes, and (repeated) ms part filling 2nd 4 bytes
		for($i = 0; $i < 4; $i ++)
			$counterBlock [$i] = $this->urs ( $nonceSec, $i * 8 ) & 0xff;
		for($i = 0; $i < 4; $i ++)
			$counterBlock [$i + 4] = $nonceMs & 0xff;
		
			// and convert it to a string to go on the front of the ciphertext
		$ctrTxt = '';
		for($i = 0; $i < 8; $i ++)
			$ctrTxt .= chr ( $counterBlock [$i] );
		
			// generate key schedule - an expansion of the key into distinct Key Rounds for each round
		$keySchedule = $this->KeyExpansion ( $key );
		
		$blockCount = ceil ( strlen ( $plaintext ) / $blockSize );
		$ciphertxt = array (); // ciphertext as array of strings
		
	
		for($b = 0; $b < $blockCount; $b ++) {
			// set counter (block #) in last 8 bytes of counter block (leaving nonce in 1st 8 bytes)
			// done in two stages for 32-bit ops: using two words allows us to go past 2^32 blocks (68GB)
			for($c = 0; $c < 4; $c ++)
				$counterBlock [15 - $c] = $this->urs ( $b, $c * 8 ) & 0xff;
			for($c = 0; $c < 4; $c ++)
				$counterBlock [15 - $c - 4] = $this->urs ( $b / 0x100000000, $c * 8 );
			
			$cipherCntr = $this->Cipher ( $counterBlock, $keySchedule ); // -- encrypt counter block --
			
	
			// block size is reduced on final block
			$blockLength = $b < $blockCount - 1 ? $blockSize : (strlen ( $plaintext ) - 1) % $blockSize + 1;
			$cipherByte = array ();
			
			for($i = 0; $i < $blockLength; $i ++) { // -- xor plaintext with ciphered counter byte-by-byte --
				$cipherByte [$i] = $cipherCntr [$i] ^ ord ( substr ( $plaintext, $b * $blockSize + $i, 1 ) );
				$cipherByte [$i] = chr ( $cipherByte [$i] );
			}
			$ciphertxt [$b] = implode ( '', $cipherByte ); // escape troublesome characters in ciphertext
		}
		
		// implode is more efficient than repeated string concatenation
		$ciphertext = $ctrTxt . implode ( '', $ciphertxt );
		$ciphertext = base64_encode ( $ciphertext );
		return $ciphertext;
	}

	/** 
	 * Decrypt a text encrypted by AES in counter mode of operation
	 *
	 * @param ciphertext source text to be decrypted
	 * @param password   the password to use to generate a key
	 * @param nBits      number of bits to be used in the key (128, 192, or 256)
	 * @return           decrypted text
	 */
	protected final function AESDecryptCtr($ciphertext, $password, $nBits) {
		$blockSize = 16; // block size fixed at 16 bytes / 128 bits (Nb=4) for AES
		if (! ($nBits == 128 || $nBits == 192 || $nBits == 256))
			return ''; // standard allows 128/192/256 bit keys
		$ciphertext = base64_decode ( $ciphertext );
		
		// use AES to encrypt password (mirroring encrypt routine)
		$nBytes = $nBits / 8; // no bytes in key
		$pwBytes = array ();
		for($i = 0; $i < $nBytes; $i ++)
			$pwBytes [$i] = ord ( substr ( $password, $i, 1 ) ) & 0xff;
		$key = $this->Cipher ( $pwBytes, $this->KeyExpansion ( $pwBytes ) );
		$key = array_merge ( $key, array_slice ( $key, 0, $nBytes - 16 ) ); // expand key to 16/24/32 bytes long
		
	
		// recover nonce from 1st element of ciphertext
		$counterBlock = array ();
		$ctrTxt = substr ( $ciphertext, 0, 8 );
		for($i = 0; $i < 8; $i ++)
			$counterBlock [$i] = ord ( substr ( $ctrTxt, $i, 1 ) );
		
			// generate key schedule
		$keySchedule = $this->KeyExpansion ( $key );
		
		// separate ciphertext into blocks (skipping past initial 8 bytes)
		$nBlocks = ceil ( (strlen ( $ciphertext ) - 8) / $blockSize );
		$ct = array ();
		for($b = 0; $b < $nBlocks; $b ++)
			$ct [$b] = substr ( $ciphertext, 8 + $b * $blockSize, 16 );
		$ciphertext = $ct; // ciphertext is now array of block-length strings
		
	
		// plaintext will get generated block-by-block into array of block-length strings
		$plaintxt = array ();
		
		for($b = 0; $b < $nBlocks; $b ++) {
			// set counter (block #) in last 8 bytes of counter block (leaving nonce in 1st 8 bytes)
			for($c = 0; $c < 4; $c ++)
				$counterBlock [15 - $c] = $this->urs ( $b, $c * 8 ) & 0xff;
			for($c = 0; $c < 4; $c ++)
				$counterBlock [15 - $c - 4] = $this->urs ( ($b + 1) / 0x100000000 - 1, $c * 8 ) & 0xff;
			
			$cipherCntr = $this->Cipher ( $counterBlock, $keySchedule ); // encrypt counter block
			
	
			$plaintxtByte = array ();
			for($i = 0; $i < strlen ( $ciphertext [$b] ); $i ++) {
				// -- xor plaintext with ciphered counter byte-by-byte --
				$plaintxtByte [$i] = $cipherCntr [$i] ^ ord ( substr ( $ciphertext [$b], $i, 1 ) );
				$plaintxtByte [$i] = chr ( $plaintxtByte [$i] );
			
			}
			$plaintxt [$b] = implode ( '', $plaintxtByte );
		}
		
		// join array of blocks into single plaintext string
		$plaintext = implode ( '', $plaintxt );
		
		return $plaintext;
	}
	
	/*
	 * Unsigned right shift function, since PHP has neither >>> operator nor unsigned ints
	 *
	 * @param a  number to be shifted (32-bit integer)
	 * @param b  number of bits to shift a to the right (0..31)
	 * @return   a right-shifted and zero-filled by b bits
	 */
	protected final function urs($a, $b) {
		$a &= 0xffffffff;
		$b &= 0x1f; // (bounds check)
		if ($a & 0x80000000 && $b > 0) { // if left-most bit set
			$a = ($a >> 1) & 0x7fffffff; //   right-shift one bit & clear left-most bit
			$a = $a >> ($b - 1); //   remaining right-shifts
		} else { // otherwise
			$a = ($a >> $b); //   use normal right-shift
		}
		return $a;
	}
}














	/**
	 * MAIL Administration Class
	 * This class represents the CRUD [Hybernate] behaviors implemented 
	 * with the Hybernate framework 
	 *
	 * @package		CLASSES::MAIL::MAIL
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 * REQUIRES MCRYPT LIB: http://php.net/manual/en/mcrypt.installation.php	
	 */	 
	
	/**
		EXAMPLE:
			// init a new instance of Crypto Class
			$crypto = new CRYPT::getInstance("PASSWORD");
			
			// get the POST data
			$messagefromflash = $_POST ["message"];
			
			// decrypt data
			$decrypted_messagefromflash = $crypto->decrypt(utf8_decode($messagefromflash));
			$intDecodedPrice = $crypto->decrypt(utf8_decode($intPrice));
			
			$strResult = $crypto->encrypt(utf8_encode($resultmsg . "|" . $intDecodedPrice));
			
			// return a crypted result to flash
			$resultmsg = "Ok, i received your data, everything is fine !";
			echo "result=".$crypto->encrypt(utf8_encode($resultmsg)) . "&decrypted=" . $crypto->decrypt(utf8_decode($strResult));
			
			
			MCRYPT_MODE_ECB 	- 	Electronic Code Book mode
			
			MCRYPT_MODE_CBC 	- 	Cipher Block Chaining mode
			
			MCRYPT_MODE_CFB 	- 	Cipher Feedback mode
			
			MCRYPT_MODE_OFB 	- 	Output Feedback mode with 8 bits of feedback
			
			MCRYPT_MODE_NOFB 	- 	Output Feedback mode with n bits of feedback, where n is the block size of the algorithm used (libmcrypt 2.4 and higher only)
			
			MCRYPT_MODE_STREAM 	- 	Stream Cipher mode, for algorithms such as RC4 and WAKE (libmcrypt 2.4 and higher only)
	*/
	class AES_CYPHER {
		protected $key = NULL;
		protected $iv = NULL;
		protected $iv_size = NULL;
		
		public static function getInstance($strKey = __ENCRYPTION_KEY__)
		{
			$objCrypt = new AES_CYPHER($strKey);
			//$objCrypt->init($strKey);
			return ($objCrypt);
		}
		
	    /** 
		 * constructor 
		 * @param $key (string:'TheKey') 
		 * @return void 
		 */ 
		function __construct($key=__ENCRYPTION_KEY__){ 
			$this->algorithm = MCRYPT_RIJNDAEL_128;/* MCRYPT_RIJNDAEL_128 MCRYPT_DES*/
			$this->mode = MCRYPT_MODE_ECB/*MCRYPT_MODE_ECB*/;
			
			$this->iv_size = mcrypt_get_iv_size($this->algorithm, $this->mode); 
			
			$this->iv = mcrypt_create_iv($this->iv_size, MCRYPT_RAND); 
			$this->key = trim($key); 
		} 
	
		public function encrypt($string){ 
			$string=trim($string); 
			return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $string, MCRYPT_MODE_ECB, $this->iv)); 
		} 
	
		public function decrypt($string){ 
			return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$this->key,base64_decode($string),MCRYPT_MODE_ECB,$this->iv)); 
		} 	
	}
	
	
	class CRYPT extends SITE_EXCEPTION
	{
		protected $key = NULL;
		protected $iv = NULL;
		protected $iv_size = NULL;
		
		public function __construct($strKey = __ENCRYPTION_KEY__)
		{
			$this->init($strKey );
		}
		
		public static function getInstance($strKey = __ENCRYPTION_KEY__)
		{
			$objCrypt = new CRYPT($strKey);
			//$objCrypt->init($strKey);
			return ($objCrypt);
		}
		
		public function init($key = "")
		{
			if (! defined("MCRYPT_DES"))
			{
				throw new Exception(__CLASS__ . " REQUIRES MCRYPT LIB: http://php.net/manual/en/mcrypt.installation.php");	
				exit();
			}
			
			$this->key = ($key != "") ? $key : "";
	
			$this->algorithm = MCRYPT_RIJNDAEL_128;/* MCRYPT_RIJNDAEL_128 MCRYPT_DES*/
			$this->mode = MCRYPT_MODE_CBC/*MCRYPT_MODE_ECB*/;
	
			$this->iv_size = mcrypt_get_iv_size($this->algorithm, $this->mode);
			$this->iv = mcrypt_create_iv($this->iv_size, MCRYPT_RAND);
			
			return (true);
		}
	
		public function encrypt($data)
		{
			$size = mcrypt_get_block_size($this->algorithm, $this->mode);
			$data = $this->pkcs5_pad($data, $size);
			return base64_encode(mcrypt_encrypt($this->algorithm, $this->key, $data, $this->mode, $this->iv));
		}
		
		public function decrypt($data)
		{
			return $this->pkcs5_unpad(rtrim(mcrypt_decrypt($this->algorithm, $this->key, base64_decode($data), $this->mode, $this->iv)));
		}
		
		public function dbEncrypt($strData = NULL)
		{
			$objDatabase  = DATABASE::getInstance();
			$arrRecordSet = $objDatabase->query("
				SELECT ENCODE('" . $strData . "', '" . __ENCRYPTION_KEY__ . "') AS EncryptedData 							 
			");
			return (count($arrRecordSet) ? $arrRecordSet[0]['EncryptedData'] : NULL);
		}
		
		public function dbDecrypt($strData = NULL)
		{
			$objDatabase  = DATABASE::getInstance();
			$arrRecordSet = $objDatabase->query("
				SELECT DECODE('" . $strData . "', '" . __ENCRYPTION_KEY__ . "') AS EncryptedData 							 
			");
			return (count($arrRecordSet) ? $arrRecordSet[0]['EncryptedData'] : NULL);
		}
		
		public function pkcs5_pad($text, $blocksize)
		{
			$pad = $blocksize - (strlen($text) % $blocksize);
			return $text . str_repeat(chr($pad), $pad);
		}
	
		public function pkcs5_unpad($text)
		{
			$pad = ord($text{strlen($text)-1});
			if ($pad > strlen($text)) return false;
			if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return $text; 
			return substr($text, 0, -1 * $pad);
		}
	}
	 
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Lol_image_formatter
{

	private $version;
	private $api_url; 

	const BASE_API_CALL = "http://ddragon.leagueoflegends.com/realms/na.json";
	const ITEM_IMAGE_URL = "/img/item";
	const CHAMPION_IMAGE_URL = "/img/champion";
	const SPELL_IMAGE_URL = "/img/spell";

	public function __construct()
	{
		if($this->api_url == NULL || $this->version == NULL)
		{
			//call the API and return the result
			$ch = curl_init(self::BASE_API_CALL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			curl_close($ch);
			$result = json_decode($result,TRUE);

			$this->version = $result['v'];
			$this->api_url = $result['cdn'];
		}
	}

	public function to_image_url($sprite, $type)
	{
		switch ($type) {
			case 'item':
				return $this->api_url . "/" . $this->version . self::ITEM_IMAGE_URL . "/" . $sprite;
				break;
			case 'champion':
				return $this->api_url . "/" . $this->version . self::CHAMPION_IMAGE_URL . "/" .$sprite;
				break;
			case 'spell':
				return $this->api_url . "/" . $this->version . self::SPELL_IMAGE_URL . "/" .$sprite;
			break;
			default:
				# code...
				break;
		}
	}
}
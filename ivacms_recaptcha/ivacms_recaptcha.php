<?php
/*
Plugin Name: Рекаптча для комментариев постов
Description: Данный плагин добавляет каптчу (Google ReCaptcha v2) от компании Google в форму добавления комментариев.
Author: Иванов Алексей
Author Uri: http://ivacms.ru
*/

class ivacms_recaptcha{

	private $site_key='';
	
	private $secret_key='';
	
	public function __construct(){
	
		add_action('init',array($this,'load'));
		
	}
	
	
	public function load(){
	
		if((string)$this->site_key !='' and (string)$this->secret_key !=''){
	
			add_action('wp_head',array($this,'add_api_recaptcha'));
			
			add_filter('preprocess_comment', array($this,'check_recaptcha'));
			
			add_filter( 'comment_form_default_fields', array($this,'add_html_recaptcha') );
			
			if(is_user_logged_in()){
			
				add_action( 'comment_form', array($this,'add_html_recaptcha_logged') );
			
			}else{
			
				add_filter( 'comment_form_default_fields', array($this,'add_html_recaptcha') );
			
			}
	
		}
	
	}
	
	public function add_html_recaptcha($fields){
	
		$fields['recaptcha']='<div class="g-recaptcha" data-sitekey="'.$this->site_key.'"></div>';
	
		return $fields;
	}
	
	public function add_html_recaptcha_logged($post_id){
	
		echo '<div class="g-recaptcha" data-sitekey="'.$this->site_key.'"></div>';
	
	}
	
	public function add_api_recaptcha(){
	
		if(is_single() or is_page()){
	
			wp_enqueue_script('ivacms_recaptcha','https://www.google.com/recaptcha/api.js');
			
		}
		
	}
	
	
	public function check_recaptcha($data){
	
		$ip=$_SERVER['REMOTE_ADDR'];
		
		$captcha=$_POST['g-recaptcha-response'];
		
		$params="secret=".$this->secret_key."&remoteip=$ip&response=$captcha";
	
		$result = file_get_contents("https://www.google.com/recaptcha/api/siteverify?".$params, false);
		
		$captcha=json_decode($result, true);
	
		if($captcha['success']==0){
		
			$text='Роботы не могут оставлять комментарии';
		
			wp_die($text);
			
		}
		
		return $data;
		
	}
	
	
	
}	
new ivacms_recaptcha;
<?php
/**
 * form
 * 
 * @category Module
 * @package AD5
 * @author AD5
 */

class AD5_Form
{
	const TEXTDOMAIN = 'ad5-form';

	const MODE_INPUT = 'MODE_INPUT';
	const MODE_CONFIRM = 'MODE_CONFIRM';
	const MODE_COMPLETE = 'MODE_COMPLETE';
	const MODE_ERROR = 'MODE_ERROR';
	const ERROR_GLOBAL = 'error_global';

	const OVERRIDE_IF_NOT_SET = 'OVERRIDE_IF_NOT_SET';
	const OVERRIDE_IF_EMPTY = 'OVERRIDE_IF_EMPTY';

	const VALIDATE_REQUIRED = 'required';
	const VALIDATE_EMAIL = 'email';
	const VALIDATE_PHONE = 'phone';
	const VALIDATE_ALPHANUMERIC = 'VALIDATE_ALPHANUMERIC';
	const VALIDATE_MIN = 'VALIDATE_MIN';
	const VALIDATE_MAX = 'VALIDATE_MAX';
	
	const FILTER_HTML = 'html';
	
	private $mode = array();
	private $fields = array();
	private $data = array();
	private $error = array();
	private $message = array();
	private $textdomain = self::TEXTDOMAIN;

	public function __construct( $textdomain = null )
	{
		$this->mode = self::MODE_INPUT;
		if ( $textdomain ) {
			$this->set_textdomain( $textdomain );
		}
	}

	public function set_textdomain( $textdomain )
	{
		$this->textdomain = $textdomain;
	}

	public function set_post_data( $data = array() )
	{
		foreach ( $this->fields as $key => $field ) {
			if ( ! empty( $data[$key] ) ) {
				$this->data[$key] = $data[$key];
			}
			if ( ! empty( $_POST[$key] ) ) {
				$this->data[$key] = $_POST[$key];
			}
			if ( ! empty( $_FILES[$key]['tmp_name'] ) ) {
				$this->data[$key] = $_FILES[$key];
			}
			if ( array_key_exists( $key, $this->data ) ) {
				if ( ! empty( $field['filter'] ) ) {
					foreach ( $field['filter'] as $filter ) {
						$this->filter_data( $key, $filter );
					}
				}
			}
		}
	}

	public function filter_data( $key, $filter )
	{
		$data = $this->data[$key];
		if ( $filter == self::FILTER_HTML ) {
			$data = wp_strip_all_tags( $data );
		}
		$this->data[$key] = $data;
	}

	public function validate()
	{
		foreach ( $this->fields as $key => $field ) {
			if ( ! empty( $field['validate'] ) ) {
				foreach ( $field['validate'] as $validate ) {
					$this->validate_data( $key, $validate );
				}
			}
		}
	}

	public function validate_data( $key, $validate )
	{
		//embty string convert into null ( defer from 0 )
		if ( ! array_key_exists( $key, $this->data ) ) {
			$data = null;
		} else if ( $this->data[$key] == "" ) {
			$data = null;
		} else {
			$data = $this->data[$key];
		}

		if ( $validate == self::VALIDATE_REQUIRED ) {
			if ( $data === null ) {
				$this->error[$key][] = __( 'Required', $this->textdomain );
			}
		} else if ( $data !== null ) {
			if ( $validate == self::VALIDATE_EMAIL ) {
				if ( ! filter_var( $data, FILTER_VALIDATE_EMAIL ) ){
					$this->error[$key][] = __( 'Invalid email', $this->textdomain );
				}
			}
			else if ( $validate == self::VALIDATE_PHONE ) {
				$tmp = trim( mb_convert_kana( $data, 'as', 'UTF-8' ) );
				$tmp = preg_replace( '/[^0-9]/', '', $tmp );
				if ( strlen( $tmp ) < 10 || strlen( $tmp ) > 11 ) {
					$this->error[$key][] = __( 'Invalid Phone Number', $this->textdomain );
				}
			}
			else if ( $validate == self::VALIDATE_ALPHANUMERIC ) {
				if ( ! preg_match( '/^[0-9a-zA-Z]*$/', $data ) ) {
					$this->error[$key][] = __( 'Use only alphanueric characters', $this->textdomain );
				}
			}
			else if ( 0 === strpos( $validate, self::VALIDATE_MIN ) ) {
				$validates = explode( ':', $validate );
				if ( 2 === count( $validates ) ) {
					$min = $validates[1];
					if ( mb_strlen( $data ) < $min ) {
						$this->error[$key][] = sprintf(__("Input %d or more characters", $this->textdomain ), $min);
					}					
				}
			}
			else if ( 0 === strpos( $validate, self::VALIDATE_MAX ) ) {
				$validates = explode( ':', $validate );
				if ( 2 === count( $validates ) ) {
					$max = $validates[1];
					if ( mb_strlen( $data ) > $max ) {
						$this->error[$key][] = sprintf(__("Input %d or less characters", $this->textdomain ), $max);
					}					
				}
			}
		}
	}

	public function send_mail( $arg ) {
		if ( ! empty( $arg['content'] ) ) {
			$content = $this->replace_mail_content( $arg['content'] );
		} else {
			return false;
		}
		if ( ! empty( $arg['to'] ) ) {
			$to = $arg['to'];
		} else {
			$to = get_option( 'admin_email' );
		}
		if ( ! empty( $arg['from'] ) ) {
			$from = $arg['from'];
		} else {
			$from = get_option( 'admin_email' );
		}
		if ( ! empty( $arg['fromname'] ) ) {
			$fromname = $arg['fromname'];
		} else {
			$fromname = $from;
		}
		if ( ! empty( $arg['subject'] ) ) {
			$subject = $arg['subject'];
		} else {
			$subject = 'Notification from ' . get_bloginfo( 'name' );
		}
		$headers = "From: {$fromname} <{$from}>" . "\r\n";
		return wp_mail( $to, $subject, $content, $headers );
	}

	private function replace_mail_content( $template ) {
		$before = array();
		$after = array();
		foreach ( $this->data as $key => $value ) {
			$before[] = '##' . $key . '##';
			$after[] = $value;
		}
		$template = str_replace( $before, $after, $template );
		return $template;
	}


	/**
	 * SETTER, GETTER類
	 */
	public function set_mode( $mode )
	{
		$this->mode = $mode;
	}

	public function is_mode( $mode )
	{
		if ( $this->mode == $mode ) {
			return true;
		} else {
			return false;
		}
	}

	public function set_fields( $fields )
	{
		$this->fields = $fields;
	}

	public function get_data( $key=null )
	{
		if ( $key ) {
			if ( is_array( $key ) ) {
				$data = $this->data;
				foreach ( $key as $k ) {
					if ( ! empty( $data[$k] ) ) {
						$data = $data[$k];
					} else {
						return "";
					}
				}
				return $data;
			} else {
				if ( ! empty( $this->data[$key] ) ) {
					return $this->data[$key];
				} else {
					return "";
				}
			}
		} else {
			return $this->data;
		}
	}

	public function set_data( $data )
	{
		$this->data = $data;
	}

	public function is_data( $key, $value, $output=null )
	{
		if ( $value == $this->get_data( $key ) ) {
			if ( $output ) {
				echo $output;
			} else {
				return true;
			}
		} else {
			if ( $output ) {
				echo "";
			} else {
				return false;
			}
		}
	}

	public function mod_data( $key, $value, $override=null )
	{
		if ( $override == self::OVERRIDE_IF_NOT_SET ) {
			if ( array_key_exists( $key, $this->data ) ) {
				return false;
			}
		} else if ( $override == self::OVERRIDE_IF_EMPTY ) {
			if ( ! empty( $this->data[$key] ) ) {
				return false;
			}
		}
		$this->data[$key] = $value;
	}

	public function get_errors( $key=null )
	{
		if ( $key ) {
			if ( ! empty( $this->error[$key] ) ) {
				return $this->error[$key];
			} else {
				return "";
			}
		} else {
			return $this->error;
		}
	}

	public function get_error( $key, $glue="" )
	{
		$errors = $this->get_errors( $key );
		if ( is_array( $errors ) ) {
			return implode( $glue, $errors );
		} else {
			return $errors;
		}
	}

	public function has_error( $key=null )
	{
		if ( $key ) {
			if ( ! empty( $this->error[$key] ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			if ( ! empty( $this->error ) ) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function set_error( $key, $error )
	{
		$this->error[$key][] = $error;
	}

	public function set_errors( $errors )
	{
		$this->error = $errors;
	}

	public function get_message( $glue = "<br>" )
	{
		if ( $this->message ) {
			return implode( $glue, $this->message );
		} else {
			return "";			
		}
	}

	public function has_message()
	{
		if ( $this->message ) {
			return true;
		} else {
			return false;			
		}
	}

	public function set_message( $message )
	{
		$this->message[] = $message;
	}

	/**
	 * 拡張バリデート定数
	 */
	static function VALIDATE_MAX( $length )
	{
		return self::VALIDATE_MAX . ':' . $length;
	}

	static function VALIDATE_MIN( $length )
	{
		return self::VALIDATE_MIN . ':' . $length;
	}
}
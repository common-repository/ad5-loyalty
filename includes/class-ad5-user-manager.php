<?php
/**
 * Insert or Update User on front
 * 
 * @category Module
 * @package AD5
 * @author AD5
 */

class AD5_User_Manager
{
	const TEXTDOMAIN = 'ad5-user-manager';

	const FILE_IMAGE = 'FILE_IMAGE';
	const FILE_PDF = 'FILE_PDF';

	const ERROR_GLOBAL = 'error_global';

	private $data = array();
	private $meta = array();
	private $attachment = array();
	private $error = array();
	private $textdomain = self::TEXTDOMAIN;

	public function __construct( $textdomain = null )
	{
		if ( $textdomain ) {
			$this->set_textdomain( $textdomain );
		}
	}

	public function set_textdomain( $textdomain )
	{
		$this->textdomain = $textdomain;
	}

	public function set_data( $key, $value )
	{
		$this->data[$key] = $value;
	}

	public function set_meta( $key, $value )
	{
		$this->meta[$key] = $value;
	}

	public function set_acf( $key, $field_id, $value )
	{
		$this->meta[ '_' . $key ] = $field_id;
		$this->meta[$key] = $value;
	}

	public function set_acf_file( $key, $field_id, $file, $file_type )
	{
		$this->attachment['acf'][$key] = array( 
			'field_id' => $field_id,
			'file' => $file,
			'file_type' => $file_type
		);
	}

	public function set_acf_image( $key, $field_id, $value )
	{
		$this->set_acf_file( $key, $field_id, $value, self::FILE_IMAGE );
	}

	public function set_tax( $key, $value )
	{
		if ( is_array( $value ) ) {
			$this->tax[$key] = $value;
		} else {
			$this->tax[$key] = array( $value );
		}
	}

	public function set_error( $key, $error ) {
		$this->error[$key][] = $error;
	}

	public function get_error( $key = null ) {
		if ( $key ) {
			return $this->error[$key];
		} else {
			return $this->error;
		}
	}

	public function insert()
	{
		$inserted = wp_insert_user( $this->data );
		if ( is_wp_error( $inserted ) ) {
			$this->convert_wp_error( $inserted );
			return false;
		}

		$this->update_info( $inserted );

		if ( ! $this->error ) {
			return $inserted;
		} else {
			//ロールバック
			wp_delete_post( $inserted, true );
			return false;
		}
	}

	public function update( $ID )
	{
		$this->data['ID'] = $ID;
		$updated = wp_update_user( $this->data );
		if ( is_wp_error( $updated ) ) {
			$this->convert_wp_error( $updated );
			return false;
		}

		$this->update_info( $updated );

		if ( ! $this->error ) {
			return $updated;
		} else {
			return false;
		}
	}

	private function update_info( $id )
	{
		if ( $this->attachment ) {
			if ( ! empty( $this->attachment['acf'] ) ) {
				foreach ( $this->attachment['acf'] as $key => $acf ) {
					try {
						$attachment_inserted = $this->insert_attachment( $acf['file'], $acf['file_type'], $id );
						if ( ! $attachment_inserted ) {
							throw new Exception( '添付失敗' );
						}
						$this->set_acf( $key, $acf['field_id'], $attachment_inserted );
					} catch ( Exception $e ) {
						$this->set_error( $key, $e->getMessage() );
					}
				}
			}
		}
		if ( $this->meta ) {
			foreach ( $this->meta as $key => $value ) {
				update_user_meta( $id, $key, $value );
			}
		}
	}

	/**
	 * @param $_FILE[key]
	 */
	private function insert_attachment( $value, $file_type, $post_id )
	{
		if ( ! isset( $value['error'] ) || ! is_int( $value['error'] ) ) {
			throw new Exception( 'パラメータ不正' );
		}
		switch ( $value['error'] ) {
			case UPLOAD_ERR_OK: // OK
				break;
			case UPLOAD_ERR_NO_FILE:   // ファイル未選択
				throw new Exception( 'ファイルが選択されていません' );
			case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
			case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過 ( 設定した場合のみ )
				throw new Exception( 'ファイルサイズが大きすぎます' );
			default:
				throw new Exception( 'ファイルアップロードのエラーが発生しました' );
		}
		if ( $value['size'] > 5000000 ) {
			throw new Exception( 'ファイルサイズが大きすぎる' );
		}

		if ( ! $ext = array_search( 
			mime_content_type( $value['tmp_name'] ),
			$this->get_mime_list( $file_type ),
			true
		 ) ) {
			throw new Exception( 'ファイル形式が不正です' );
		}

		$prefix = is_user_logged_in() ? wp_get_current_user()->get( 'ID' ) : openssl_random_pseudo_bytes( 6 );
		$upload_dir = wp_upload_dir();
		$attachment_file = $upload_dir['path'] . '/'. $prefix . '_' . date( 'YmdHis' ) . '.' . $ext;
		if ( ! move_uploaded_file( $value['tmp_name'], $attachment_file ) ) {
			throw new Exception( 'ファイル保存失敗' );
		}
   
		$attachment_info = array( 
			'post_mime_type' => wp_check_filetype( basename( $attachment_file ), null )['type'],
			'post_title' => sanitize_file_name( basename( $attachment_file ) ),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attachment_id = wp_insert_attachment( $attachment_info, $attachment_file, $post_id );
		if ( ! $attachment_id ) {
			throw new Exception( 'ファイル保存失敗' );
		}
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $attachment_file );
		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );
   
		return $attachment_id;
	}

	private function get_mime_list( $file_type )
	{
		if ( $file_type == self::FILE_IMAGE ) {
			return array( 
				'gif' => 'image/gif',
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
			);
		}
		if ( $file_type == self::FILE_PDF ) {
			return array( 
				'pdf' => 'application/pdf'
			);
		}
		return array();
	}

	private function convert_wp_error( $wp_error )
	{
		if ( ! empty( $wp_error->errors ) ) {
			foreach ( $wp_error->errors as $error_key => $error ) {
				switch ( $error_key ) {
					case 'existing_user_email':
						$this->set_error( 'user_email', __( 'email already registered', $this->textdomain ) );
						break;
					default:
						$this->set_error( self::ERROR_GLOBAL, join( '', $error ) );
				}
			}
		}
	}
}
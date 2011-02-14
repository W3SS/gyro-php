<?php
require_once dirname(__FILE__) . '/imailmessagebuilder.cls.php';

/**
 * Build message body for a message containing attachments 
 */
class AttachmentsBuilder implements IMailMessageBuilder {
	/**
	 * Builder for the message body
	 * 
	 * @var IMailMessageBuilder
	 */
	protected $message_builder;
	
	/**
	 * Attachments as associative array of name to filename
	 * 
	 * @var array
	 */
	protected $attachments;
	
	/**
	 * Boundary to sperate attachments
	 * 
	 * @var string
	 */
	protected $boundary;
	
	/**
	 * Constructors
	 * 
	 * @param IMailMessageBuiler $msg_builder Builder for the message body
	 * @param array $attachments Attachments as associative array of name to filename
	 */
	public function __construct(IMailMessageBuilder $msg_builder, $attachments) {
		$this->message_builder = $msg_builder;
		$this->attachments = $attachments;
		$this->boundary = 'GYROMAILSEP-' . sha1(uniqid());
	}
	
	/**
	 * Return mime type of mail as a whole
	 * 
	 * @return string
	 */
	public function get_mail_mime() {
		return  'multipart/mixed; boundary="' . $this->boundary . '"';
	}
	
	/**
	 * Return mail body
	 * 
	 * @return string
	 */
	public function get_body() {
		$blocks = array();
		$blocks[] = $this->create_block($this->message_builder->get_mail_mime(), false, $this->message_builder->get_body());
		foreach($this->attachments as $name => $file) {
			$blocks[] = $this->create_attachment_block($name, $file);
		} 		
		return 
			$this->start_seperator($this->boundary) .
			implode($this->start_seperator($this->boundary), $blocks) .
			$this->end_seperator($this->boundary);
	}
	
	/**
	 * Returns generated boundary
	 * 
	 * @return string
	 */
	public function get_boundary() {
		return $this->boundary;
	}
	
	/**
	 * Return seperator 
	 */
	protected function start_seperator($boundary) {
		return "\n\n--" . $boundary . "\n";
	}
	
	/**
	 * Return seperator 
	 */
	protected function end_seperator($boundary) {
		return "\n--" . $boundary . "--\n";
	}

	/**
	 * Create a block for an attachment 
	 */
	protected function create_attachment_block($name, $file) {
		return $this->create_block(
			$this->get_attachment_mime($file) . '; name=' . $name,
			'base64',
			base64_encode(file_get_contents($file))
		);		
	}	
	
	/**
	 * Create a block in the body 
	 */
	protected function create_block($mime_type, $encoding, $content, $more_headers = array()) {
		$ret = '';
		$header = $more_headers;
		$header[] = 'Content-type: ' . $mime_type;
		if ($encoding) {
			$header[] = 'Content-Transfer-Encoding: ' . $encoding;
		} 	
		$header[] = '';
		
		$ret = implode("\n", $header) . "\n" . $content;
		return $ret;
	}
	
	/**
	 * Figure out content mime type of file
	 * 
	 * @param string Filename
	 * @return string Mime Type
	 */	
	private function get_attachment_mime($file) {
		if (function_exists('mime_content_type')) {
			return mime_content_type($file);
		}
		return 'application/octet-stream';
	}	
}
<?php
// Basic HTML generating class
class html {
	public string $tag = 'div';
	protected array $attr = array();
	protected array $inner_html = array();
	public bool $short_tag = FALSE;
	public bool $close_tag = TRUE;
	public bool $verbose = TRUE;
	public function __construct( string $tag, $id = NULL ) {
		$this->tag = $tag;
		if( !empty( $id ) && ( is_string( $id ) || is_numeric( $id ) ) ) {
			$this->attr( 'id', $id );
		}
	}
	public function __toString(): string {
		$eol = $this->verbose ? PHP_EOL : '';
		$ind = $this->verbose ? "\t" : '';
		$output = '<' . $this->tag;
		if( !empty( $this->attr ) ) {
			$output .= ' ';
			foreach( $this->attr as $attr => $value ) {
				$output .= $attr . '=';
				$output .= !is_bool( $value ) ? '"' . (string) $value . '" ' : (string) $value;
			}
			$output = mb_substr( $output, 0, -1 );
		}
		if( !$this->short_tag && $this->close_tag ) {
			$output .= empty( $this->inner_html ) ? '>' : '>' . $eol;
			foreach( $this->inner_html as $child ) {
				$child_output = (string) $child;
				$output .= $ind . preg_replace( '/\n/', "\n" . $ind, $child_output ) . $eol;
			}
			$output .= '</' . $this->tag . '>';
		} else {
			$output .= $this->short_tag ? '/>' : '>';
		}
		return $output;
	}
	public function __get( string $prop ) {
		return $this->$prop;
	}
	public function attr( string $name, $value ): bool {
		if( !is_object( $value ) && !is_array( $value ) ) {
			$this->attr[$name] = $value;
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function clear( string $name ): bool {
		unset( $this->attr[$name] );
		return TRUE;
	}
	public function append( string $name, $child ): bool {
		if( $child instanceof html || is_string( $child ) ) {
			$this->inner_html[$name] = $child;
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function drop( string $name ) {
		unset( $this->inner_html[$name] );
		return TRUE;
	}
}
<?php

class SC_Normalize {
    
    protected $texto='';
    
    protected $a    = array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä');
    protected $ar   = array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A');
    protected $e    = array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë');
    protected $er   = array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E');
    protected $i    = array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î');
    protected $ir   = array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I');
    protected $o    = array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô');
    protected $oR   = array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O');
    protected $u    = array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü');
    protected $ur   = array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U');
    protected $n    = array('ñ', 'Ñ');
    protected $nr   = array('n', 'N');
    protected $c    = array('ç', 'Ç');
    protected $cr   = array('c', 'C');
    protected $specialChars = array(
             "\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ":",
             ".", " ");
    
    public function __construct() {
        
    }
    
    private function normalize_a() {
        $this->texto = str_replace(
            $this->a,
            $this->ar,
            $this->texto
        );
    }
    
    private function normalize_e() {
        $this->texto = str_replace(
            $this->e,
            $this->er,
            $this->texto
        );
    }
    
    private function normalize_i() {
        $this->texto = str_replace(
            $this->i,
            $this->ir,
            $this->texto
        );
    }
    
    private function normalize_o() {
        $this->texto = str_replace(
            $this->o,
            $this->oR,
            $this->texto
        );
    }
    
    private function normalize_u() {
        $this->texto = str_replace(
            $this->u,
            $this->ur,
            $this->texto
        );
    }
    
    private function normalize_n() {
        $this->texto = str_replace(
            $this->n,
            $this->nr,
            $this->texto
        );
    }
    
    private function normalize_c() {
        $this->texto = str_replace(
            $this->c,
            $this->cr,
            $this->texto
        );
    }
    
    private function normalize_specialChars() {
        $this->texto = str_replace(
            $this->specialChars,
            '',
            $this->texto
        );
    }
    
    public function init( $texto ) {
        
        $this->texto = trim($texto);
        
        // Llamando a métodos limpiadores
        $this->normalize_a();
        $this->normalize_e();
        $this->normalize_i();
        $this->normalize_o();
        $this->normalize_u();
        $this->normalize_n();
        $this->normalize_c();
        $this->normalize_specialChars();
        
        return $this->texto;
    }
    
}
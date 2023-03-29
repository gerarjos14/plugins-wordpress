<?php 

class BC_FAQ{

    private $top;
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->top = 4;
    }

    public function seeder(){
        for ($i=0; $i < $this->top; $i++) { 
            $this->db->insert(BC_TABLE_FAQ, [
                'pregunta_faq' => 'Pregunta ',
                'answer'   => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Maiores fugiat beatae a nisi inventore. Esse, nostrum. Ipsa quod veritatis consectetur quos beatae voluptatibus est doloremque odio praesentium nobis',
            ]);
        }
    }
}
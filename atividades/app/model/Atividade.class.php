<?php
/**
 * Atividade Active Record
 * @author  <your-name-here>
 */
class Atividade extends TRecord
{
    const TABLENAME = 'atividade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $tipo_atividade;
    private $ticket;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_atividade');
        parent::addAttribute('hora_inicio');
        parent::addAttribute('hora_fim');
        parent::addAttribute('descricao');
        parent::addAttribute('colaborador_id');
        parent::addAttribute('tipo_atividade_id');
        parent::addAttribute('ticket_id');
    }

    public function retornaTotalAtividadesColaborador($colaborador, $mes, $ano)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query("select sum((a.hora_fim - a.hora_inicio)) as total from atividade as a
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.colaborador_id = {$colaborador}
                                ");
        
        foreach ($result as $row)
        {
            $data = $row['total'];
        }
        
        return $data;
        
    }

    public function retornaAtividadesColaborador($colaborador, $mes, $ano)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query("select a.tipo_atividade_id,t.nome, sum((a.hora_fim - a.hora_inicio)) as total from atividade as a
                                inner join tipo_atividade as t on a.tipo_atividade_id = t.id
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.colaborador_id = {$colaborador}
                                group by tipo_atividade_id, nome
                                order by nome
                                ");
        
        return $result;
        
    }
    
    public function retornaAtividadesSistemaColaborador($colaborador, $mes, $ano)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query("select t.sistema_id, s.nome, sum((a.hora_fim - a.hora_inicio)) as total from atividade as a 
                                inner join ticket as t on a.ticket_id = t.id
                                inner join sistema as s on t.sistema_id = s.id
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.colaborador_id = {$colaborador}
                                group by t.sistema_id, s.nome
                                order by s.nome
                                ");
        
        return $result;
        
    }
    
    public function retornaAtividadesClienteColaborador($colaborador, $mes, $ano)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query("select t.solicitante_id, sum((a.hora_fim - a.hora_inicio)) as total from atividade as a 
                                inner join ticket as t on a.ticket_id = t.id
                                where extract('month' from a.data_atividade) = {$mes} and extract('year' from a.data_atividade) = {$ano} and a.colaborador_id = {$colaborador}
                                group by solicitante_id
                                ");
        
        return $result;
        
    }
    
    public function retornaUltimaAtividade($user)
    {
        
        $conn = TTransaction::get();
        $result = $conn->query('SELECT id FROM atividade WHERE colaborador_id = '.$user.' order by data_atividade desc, id desc limit 1');
        
        foreach ($result as $row)
        {
            $data = $row['id'];
        }
        
        return $data;
        
    }
    
    /**
     * Method set_tipo_atividade
     * Sample of usage: $atividade->tipo_atividade = $object;
     * @param $object Instance of TipoAtividade
     */
    public function set_tipo_atividade(TipoAtividade $object)
    {
        $this->tipo_atividade = $object;
        $this->tipo_atividade_id = $object->id;
    }
    
    /**
     * Method get_tipo_atividade
     * Sample of usage: $atividade->tipo_atividade->attribute;
     * @returns TipoAtividade instance
     */
    public function get_tipo_atividade()
    {
        // loads the associated object
        if (empty($this->tipo_atividade))
            $this->tipo_atividade = new TipoAtividade($this->tipo_atividade_id);
    
        // returns the associated object
        return $this->tipo_atividade;
    }
    
    
    /**
     * Method set_ticket
     * Sample of usage: $atividade->ticket = $object;
     * @param $object Instance of Ticket
     */
    public function set_ticket(Ticket $object)
    {
        $this->ticket = $object;
        $this->ticket_id = $object->id;
    }
    
    /**
     * Method get_ticket
     * Sample of usage: $atividade->ticket->attribute;
     * @returns Ticket instance
     */
    public function get_ticket()
    {
        // loads the associated object
        if (empty($this->ticket))
            $this->ticket = new Ticket($this->ticket_id);
    
        // returns the associated object
        return $this->ticket;
    }
    


}
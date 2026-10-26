<?php
/**
 * Rode uma vez: php seed_demo.php
 * Cria usuário demo + dados de exemplo.
 */
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/helpers.php';
require __DIR__ . '/app/core/Database.php';
require __DIR__ . '/app/models/User.php';
require __DIR__ . '/app/models/Contact.php';
require __DIR__ . '/app/models/Event.php';
require __DIR__ . '/app/models/Task.php';
require __DIR__ . '/app/models/Group.php';

$db = Database::connect();
$users = new User($db);

if ($users->byEmail('demo@connecta.app')) {
    echo "Usuário demo já existe.\n";
    exit;
}

$users->create('Thales Berado', 'demo@connecta.app', 'senha1234', 'Empresa Mercado Livre', 'Vendedor');
$user = $users->byEmail('demo@connecta.app');
$uid = (int) $user['id'];

$contacts = new Contact($db);
$contacts->save(['name'=>'Arthur Souza','phone'=>'+5563999889879','email'=>'arthursouza@gmail.com','whatsapp'=>'+5563999889879','telegram'=>'@arthur','teams'=>'arthur.souza','status'=>'Ativo'], $uid);
$contacts->save(['name'=>'Gustavo Milh.','phone'=>'+5563999886545','email'=>'gustavomilh@mens@gmail.com','whatsapp'=>'+5563999886545','status'=>'Ativo'], $uid);
$contacts->save(['name'=>'Beatriz Alves','phone'=>'+5563992345678','email'=>'beatriz.alves@empresa.com','whatsapp'=>'+5563992345678','telegram'=>'@bia','status'=>'Ativo'], $uid);

$events = new Event($db);
$today = date('Y-m-d');
$events->save(['title'=>'Reunião de Alinhamento – Projeto Phoenix','description'=>'Discussão sobre o cronograma de entrega e recursos necessários para o Q4.','event_date'=>$today,'start_time'=>'09:00','location'=>'Google Meet','category'=>'Trabalho'], $uid);
$events->save(['title'=>'Coffee Chat com Dr. Silva','description'=>'Conversa informal sobre novas tendências no mercado de SaaS e parcerias.','event_date'=>$today,'start_time'=>'14:30','location'=>'Starbucks Centro','category'=>'Networking'], $uid);
$events->save(['title'=>'Webinar: Design Systems no Connecta','description'=>'Apresentação sobre a evolução dos tokens de design e componentes compartilhados.','event_date'=>$today,'start_time'=>'16:00','category'=>'Estudo'], $uid);

$tasks = new Task($db);
$tasks->save(['title'=>'Reunião de Alinhamento','priority'=>'alta','status'=>'pendente','due_date'=>$today,'due_time'=>'14:00'], $uid);
$tasks->save(['title'=>'Enviar Proposta Comercial','priority'=>'media','status'=>'concluida','due_date'=>$today,'due_time'=>'09:30'], $uid);
$tasks->save(['title'=>'Revisão de Metas Mensais','priority'=>'media','status'=>'pendente','due_date'=>date('Y-m-d',strtotime('+1 day')),'due_time'=>'10:00'], $uid);
$tasks->save(['title'=>'Treinamento Novos Colaboradores','priority'=>'baixa','status'=>'pendente','due_date'=>date('Y-m-d',strtotime('+2 day')),'due_time'=>'15:30'], $uid);

$groups = new Group($db);
$groups->save(['name'=>'Equipe de Vendas Matriz','description'=>'Marcos: Olá pessoal, as metas do mês foram atualizadas no drive','tag'=>'Faturamento','color'=>'#3B82F6','icon'=>'chart'], $uid);
$groups->save(['name'=>'Novos Lançamentos 2024','description'=>'Você: O catálogo digital já está disponível para compartilhamento','tag'=>'Marketing','color'=>'#10B981','icon'=>'rocket'], $uid);
$groups->save(['name'=>'Treinamento Soft Skills','description'=>'RH: Link do certificado de conclusão disponível no portal','tag'=>'Educação','color'=>'#F59E0B','icon'=>'book'], $uid);
$groups->save(['name'=>'Promoções Relâmpago (VIP)','description'=>'Transmissão: 124 pessoas receberam a atualização','tag'=>'VIP','color'=>'#8B5CF6','icon'=>'users'], $uid);

// Add sample messages
$gList = $groups->all($uid);
foreach ($gList as $g) {
    if ($g['description']) {
        $groups->addMessage((int)$g['id'], $uid, $g['description']);
    }
}

echo "Demo criado!\n";
echo "E-mail: demo@connecta.app\n";
echo "Senha:  senha1234\n";

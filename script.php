<?php

/**
 * Limpa os arquivos modificados pelo malware c3284d
 * 
 * @author Diego Saouda <diegosaouda@gmail.com>
 */

//alguns servidores não permite manipular essa variável
//somente para debug de desenvolvimento
//ini_set('display_errors', 1);


//aqui são configurações

$cont_arquivo_limpo = 0;
$cont_arquivo_nao_limpo = 0;
$cont_arquivo = 0;
$cont_arquivo_infectado = 0;

//true se achar alguma coisa remove
//false só exibir problema na tela
$remover = false;
if(isset($_GET['remover'])){
    $remover = true;
}


//busca rápida pelo malware
$search = 'c3284d';

//busca completa pelo trecho modificado
$regex = array(
    '.html' => '/<!--c3284d-->(.*)<!--\/c3284d-->/is' ,
    '.htm'  => '/<!--c3284d-->(.*)<!--\/c3284d-->/is' ,
    '.asp'  => '/<!--c3284d-->(.*)<!--\/c3284d-->/is' ,
    '.js'   => '/\/\*c3284d\*\/(.*)\/\*\/c3284d\*\//is' ,
    '.php'  => '/#c3284d#(.*)#\/c3284d#/is',
);

//verifica somente os arquivos que o malware age.
$allow = '/\.(php|asp|htm|html|js)$/im';


//verificando...
$tempo_inicio = time()+microtime();

$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '*';

echo 'diretorio atual: ' . $dir;
echo "<br /><br />";

$files = recursive_glob($dir);

echo '<table border="1">';
echo '<tr>';
    echo '<td>Data Modificação</td>';
    echo '<td>Arquivo</td>';    
echo '</tr>';
foreach($files as $file){
  
  if(is_dir($file)){
    continue;
  }
  
  if(!preg_match($allow, $file)){
    continue;
  }
  
  if(preg_match('/_teste.php$/', $file)){
    continue;
  }
  
  $cont_arquivo++;
  
  $content = file_get_contents($file);
  
  if (strpos($content, $search) === false){
    continue;
  }
    
  $extensao = substr($file,strrpos($file, '.'));
  
  echo '<tr>';
    echo '<td>' . date('d/m/Y H:m:s', filemtime($file)) . '</td>';        
    echo '<td>';  
    echo $file;
  
    
  if(isset($regex[$extensao])){      
      preg_match($regex[$extensao],$content,$matches);
      if (isset($matches[0])) {
          
          $cont_arquivo_infectado++;
          
          echo '<span style="font-size: 10px; color: red;">';
          echo '<pre>';
          echo htmlentities($matches[0]);
          echo '</pre>';
          echo '</span>';
      }
  }
  
  
  
  if($remover && isset($matches[0])){
      if(isset($regex[$extensao])){
          echo 'Status clean: ';            
          $content = preg_replace($regex[$extensao],'', $content,1);
          if(@file_put_contents($file, $content)){
              echo 'removido, arquivo limpo';
              $cont_arquivo_limpo++;
          }  else {
              echo 'arquivo não limpo, permissão de escrita não é válida';
              $cont_arquivo_nao_limpo++;
          }
      }
      
  }
  
  echo "</td>";
  echo "</tr>";
  
}

echo '</table>';

$tempo_fim = time()+microtime();

 

echo '<br />Total de arquivos: '   . '<strong>'. $cont_arquivo                . '</strong>' ;
echo '<br />Total de infectados: ' . '<strong>'.  $cont_arquivo_infectado     . '</strong>' ;
if($remover){
    echo '<br />Total de limpos: '     . '<strong>'. $cont_arquivo_limpo          . '</strong>' ;
    echo '<br />Total de limpos que não foram limpos: <strong>' . $cont_arquivo_nao_limpo . '</strong>' ;
}
echo '<br />Restam <strong>' . ($cont_arquivo_infectado - $cont_arquivo_limpo) . '</strong> arquivos para limpar';

//opa, finalizou...
echo '<br />concluido em ' . sprintf('%.2f', ($tempo_fim - $tempo_inicio) . ' segundos');


function recursive_glob($path){
  
  static $files = array();
  
  $glob = glob($path);
  if($glob){
      foreach($glob as $file){    
        
        if($file === '.' || $file === '..'){
          continue;
        }
        
        $files[] = $file;
        
        if(is_dir($file)){                  
          recursive_glob($file . DIRECTORY_SEPARATOR . '*');      
        }    
      }
  }
  
  return $files;
}


<?php

namespace PDF;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\SecurityChecker;
use Dompdf\Dompdf;
use Dompdf\Options;
use ErrorLogger\ErrorLogger;
use FileHandler\FileHandler;
use GlobalsFunctions\Globals;
use Mpdf\Mpdf;

class PDF
{
    private string $tempFilePath;
    private string $author;
    private string $creator;
    private string $htmlContent;
    private string $subject;
    private string $styles;
    private string $keywords;
    private $fileOutput;

    private mPdf $mPdf;
    private $title;

    /**
     * @return Mpdf
     */
    public function getMPdf(): Mpdf
    {
        return $this->mPdf;
    }

    /**
     * @param Mpdf $mPdf
     */
    public function setMPdf(Mpdf $mPdf): void
    {
        $this->mPdf = $mPdf;
    }

    /**
     * @return string
     */
    public function getTempFilePath(): string
    {
        return $this->tempFilePath;
    }

    /**
     * @param string $tempFilePath
     */
    public function setTempFilePath(string $tempFilePath): void
    {
        $this->tempFilePath = $tempFilePath;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getCreator(): string
    {
        return $this->creator;
    }

    /**
     * @param string $creator
     */
    public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }

    /**
     * @return string
     */
    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    /**
     * @param string $htmlContent
     */
    public function setHtmlContent(string $htmlContent): void
    {
        $this->htmlContent = $htmlContent;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getStyles(): string
    {
        return $this->styles;
    }

    /**
     * @param string $styles
     */
    public function setStyles(string $styles): void
    {
        $this->styles = $styles;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return mixed
     */
    public function getFileOutput()
    {
        return $this->fileOutput;
    }

    public function pdf($toViewBrowser = false, $saveTodb = false){
        $this->mPdf->setCreator($this->getCreator());
        $this->mPdf->setAuthor($this->getAuthor());
        $this->mPdf->setTitle($this->getTitle());
        $this->mPdf->setSubject($this->getSubject());
        $this->mPdf->setKeywords($this->getKeywords());
        $this->mPdf->WriteHTML($this->getStyles(), 1);
        $this->mPdf->WriteHTML($this->getHtmlContent(), 2);
        $file = 'Files/pdf-'.date('Y-m-d-h-i-s').'.pdf';
        $this->mPdf->OutputFile($file);
        if($toViewBrowser){
            $this->mPdf->Output();
        }
        if($saveTodb){
            $d = stat($file);
            $pathinfo = pathinfo($file);
            $data = [
                'filename'=>$pathinfo['filename'],
                'filesize'=>$d['size'],
                'fileBOB'=>file_get_contents($file),
                'owner'=>'unknown',
                'time'=>$d['mtime'],
                'type'=>$pathinfo['extension'],
                'path'=>$file,
                'uri'=>Globals::protocal().'://'.Globals::serverHost().'/'.Globals::home().'/'.$file
            ];
            try{
             $fid = Insertion::insertRow($this->pdfSchema()['table'],$data);
             $this->setFileOutput($file);
             return [
               'path'=>$file,
                 'fid'=>$fid
             ];
            }catch (\Throwable $e){
                ErrorLogger::log($e);
            }
        }else{
            return $file;
        }

    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @param mixed $fileOutput
     */
    public function setFileOutput($fileOutput): void
    {
        $this->fileOutput = $fileOutput;
    }

   public function pdfSchema(){
        $columns= ['fid','filename','filesize','fileBOB','owner','time','type', 'path','uri'];
        $attributes = [
            'fid'=>['int(11)','auto_increment','primary key'],
            'filename'=>['varchar(50)','not','null'],
            'filesize'=>['int(11)','null'],
            'fileBOB'=>['longblob','not','null'],
            'owner'=>['varchar(50)', 'null'],
            'time'=>['varchar(50)','null'],
            'type'=>['varchar(20)', 'null'],
            'path'=>['varchar(250)', 'null'],
            'uri'=>['varchar(250)','null']
            ];
        return ['col'=>$columns,'att'=>$attributes,'table'=>'pdf_documents'];
   }

   public function __construct()
   {
       if(SecurityChecker::isConfigExist()){
          if(Database::database()){
              $maker = new MysqlDynamicTables();
              $maker->resolver(Database::database(),
                  $this->pdfSchema()['col'],
                  $this->pdfSchema()['att'],
                  $this->pdfSchema()['table'],
                  false
              );
          }
       }
   }

   public static function makePdf(string $title, string $name,
                           string $author, string $creator, string $htmlContent,
                           array $keywords= [], string $subject = "",
                           string $styles = "",bool $stylesIsFile = false, bool $saveToDb = false,
                           bool $toViewBrowser = false){
       $plugin = new mPdf;
       $pdf = new \PDF\PDF();
       $pdf->setMPdf($plugin);
       $pdf->setTitle($title);
       $pdf->setCreator($creator);
       $pdf->setAuthor($author);
       $pdf->setKeywords(implode(',', (array_values($keywords))));
       $pdf->setSubject($subject);
       $pdf->setStyles($stylesIsFile ? file_get_contents($styles) : $styles);
       $pdf->setHtmlContent($htmlContent);
       return $pdf->pdf($toViewBrowser,$saveToDb);
   }

}
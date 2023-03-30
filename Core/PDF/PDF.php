<?php

namespace PDF;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Dompdf\Dompdf;
use Dompdf\Options;
use FileHandler\FileHandler;
use GlobalsFunctions\Globals;

class PDF
{
   private string $contentText;

    private string $filename;
    private string $fileMetaDataTitle;
    private string $fileMetaAuthor;
    private string $fileMetaSubject;

    private string $fileOrientation;

    /**
     * @return string
     */
    public function getFileMetaAuthor(): string
    {
        return $this->fileMetaAuthor;
    }

    /**
     * @param string $fileMetaAuthor
     */
    public function setFileMetaAuthor(string $fileMetaAuthor): void
    {
        $this->fileMetaAuthor = $fileMetaAuthor;
    }

    /**
     * @return string
     */
    public function getFileMetaSubject(): string
    {
        return $this->fileMetaSubject;
    }

    /**
     * @param string $fileMetaSubject
     */
    public function setFileMetaSubject(string $fileMetaSubject): void
    {
        $this->fileMetaSubject = $fileMetaSubject;
    }

    /**
     * @return string
     */
    public function getFileOrientation(): string
    {
        return $this->fileOrientation;
    }

    /**
     * @param string $fileOrientation
     */
    public function setFileOrientation(string $fileOrientation): void
    {
        $this->fileOrientation = $fileOrientation;
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType(string $fileType): void
    {
        $this->fileType = $fileType;
    }
    private string $fileType;
    private string $templatePath;

    private Dompdf $domObject;

    private Options $optionsObject;

    /**
     * @return Options
     */
    public function getOptionsObject(): Options
    {
        return $this->optionsObject;
    }

    /**
     * @param Options $optionsObject
     */
    public function setOptionsObject(Options $optionsObject): void
    {
        $this->optionsObject = $optionsObject;
    }

    /**
     * @return Dompdf
     */
    public function getDomObject(): Dompdf
    {
        return $this->domObject;
    }

    /**
     * @param Dompdf $domObject
     */
    public function setDomObject(Dompdf $domObject): void
    {
        $this->domObject = $domObject;
    }

    /**
     * @return string
     */
    public function getContentText(): string
    {
        return $this->contentText;
    }

    /**
     * @param string $contentText
     */
    public function setContentText(string $contentText): void
    {
        $this->contentText = $contentText;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFileMetaDataTitle(): string
    {
        return $this->fileMetaDataTitle;
    }

    /**
     * @param string $fileMetaDataTitle
     */
    public function setFileMetaDataTitle(string $fileMetaDataTitle): void
    {
        $this->fileMetaDataTitle = $fileMetaDataTitle;
    }

    /**
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * @param string $templatePath
     */
    public function setTemplatePath(string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    public function buildPDF(){
        $this->domObject->setPaper($this->getFileType(), $this->getFileOrientation());
        $this->domObject->loadHtml($this->contentText);
        $this->domObject->render();
    }

    public function showOnBrowser(){
        $this->domObject->stream($this->filename,['attachment'=>0]);
    }

    public function buildFromHtml(){
        $this->domObject->loadHtml($this->getContentText());
    }

    public function init(Options $options){
        $this->setOptionsObject($options);
        $this->domObject = new Dompdf($this->getOptionsObject());
    }

    public function setPdfProperties(){
        $this->domObject->addInfo('Title',$this->getFileMetaDataTitle());
        $this->domObject->addInfo('Author',$this->getFileMetaAuthor());
    }

    public function loadTemplateFile(){
        $this->domObject->loadHtmlFile($this->getTemplatePath());
    }

    public function actualFile(){
        return $this->domObject->output();
    }

    public static function pdf($content, $title='none',$author='none', $filename='document.pdf', $orientation='landscape', $size = 'A4', $externalCss = false, $toBrowser = true, $saveType = 'file'){
        $option = new Options;
        $option->setChroot(__DIR__);
        $option->setIsRemoteEnabled($externalCss);
        $pdf = new PDF;
        $pdf->init($option);
        $pdf->setFileMetaDataTitle($title);
        $pdf->setFileMetaAuthor($author);
        $pdf->setFileOrientation($orientation);
        $pdf->setFileType($size);
        $pdf->setFilename($filename);
        $pdf->setContentText($content);
        $pdf->buildPDF();
        $pdf->setPdfProperties();

        if($toBrowser === true){
            $pdf->showOnBrowser();
        }
        if($saveType === 'file'){
            $pdf->getDomObject()->stream();
            $base = Globals::root().'/Files';
            $url = FileHandler::saveFile($filename,$content,'binary');
            $list = explode('/',$url);
            $complete = $base.'/Files/'.end($list);
            return ['path'=>$complete, 'url'=>$url];
        }
        if($saveType ==='database'){
            $schema = self::pdfSchema();
            $maker = new MysqlDynamicTables();
            $maker->resolver(Database::database(),$schema['col'],$schema['att'],$schema['table'],false);
            $output = $pdf->actualFile();
            $data = [
                'filename'=>$filename,
                'filesize'=>filesize($output),
                'fileBOB'=>$output
            ];
            return Insertion::insertRow($schema['table'],$data);
        }
    }

   public static function pdfSchema(){
        $columns= ['fid','filename','filesize','fileBOB'];
        $attributes = [
            'fid'=>['int(11)','auto_increment','primary key'],
            'filename'=>['varchar(50)','not','null'],
            'filesize'=>['int(11)','null'],
            'fileBOB'=>['longblob','not','null']
            ];
        return ['col'=>$columns,'att'=>$attributes,'table'=>'pdf_documents'];
   }
}
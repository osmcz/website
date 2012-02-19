<?php
/**
 * nPress - opensource cms
 *
 * @copyright  (c) 2012 Pavel Zbytovský (pavel@zby.cz)
 * @link       http://npress.info/
 * @package    nPress
 */


/** Files presenter
 *
 * Works just as a service for @see File objects (link methods)
 *
 */
class Front_FilesPresenter extends Front_BasePresenter
{
	/** Handle the file downloading
	 * @param $id   contains fileid-size
	 */
	public function actionDefault($id)
	{
		$file = FilesModel::getFile($id);
		$response = $file->getDownloadHttpResponse();
		$this->sendResponse($response);
	}


	/** Handle thumbnail resizing and caching
	 *
	 * for better performance use special route like:
	 * `Route('data/thumbs/<id>[_<opts>].png', 'Files:preview');`
	 *
	 * @param type $id database file ID
	 * @param type $opts ex: 120x120_square_fadeframe-30_nocache ...
	 */
	public function actionPreview($id, $opts=null)
	{
		$file = FilesModel::getFile($id);
		$response = $file->getPreviewHttpResponse($opts);
		$this->sendResponse($response);
	}
	
	public function actionPreviewPage($id){
		$file = FilesModel::getFile($id);
		$this->template->file = $file;
		$this->setLayout(false);

		//documentFile
		$xml = substr($file->info,5);
		$sxml = simplexml_load_string( $xml ); //todo: proč nefunguje?
		
		$pages = array();
		if($sxml && $sxml->page)
		foreach($sxml->page as $p){
			$page = array($p, array());
			foreach($p->block as $b){
				foreach($b->text as $t){
					$page[1][] = $t;
				}
			}

			$pages[] = $page;
		}
		$this->template->pdf2xml = $pages;
	}

}
<?php

namespace Acme\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageUploadController extends Controller{
    public function handleAction(){
        $request = $this->getRequest();
        try{
        /* @var UploadedFile $uploadedFile*/
        $uploadedFile = $request->files->get('upload');
        if (null === $uploadedFile)
            return new RedirectResponse($this->generateUrl('homepage'));
        $callback = $request->get('CKEditorFuncNum');
        $rootPath = $this->getRequest()->server->get('DOCUMENT_ROOT') .
            $this->container->getParameter('page_upload_dir');
        $fileName = sha1( uniqid( mt_rand(), true ) ) . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move($rootPath, $fileName);
            $httpPath = $this->container->getParameter('page_upload_dir')."/".$fileName;
            $error = '';
        }catch(\Exception $e){
            $error = var_export($e,true);
            $httpPath = '';
        }

        return new Response("<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction($callback, '$httpPath', '$error');</script>");
    }
}
 
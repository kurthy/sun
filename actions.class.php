<?php

//require_once dirname(__FILE__).'/../lib/BasesfGuardUserActions.class.php';
//require_once dirname(__FILE__).'/../lib/sfGuardUserGeneratorConfiguration.class.php';
//require_once dirname(__FILE__).'/../lib/sfGuardUserGeneratorHelper.class.php';

/**
 * sfGuardUser actions.
 *
 * @package    sfGuardPlugin
 * @subpackage sfGuardUser
 * @author     Fabien Potencier
 * @version    SVN: $Id: actions.class.php 12896 2008-11-10 19:02:34Z fabien $
 */
//class sfGuardUserActions extends BasesfGuardUserActions
class sfGuardUserActions extends autosfGuardUserActions
{
/*
  public function executeConfirm(sfWebRequest $request)
  {
    $this->sf_guard_user = $this->getRoute()->getObject();
    if($this->sf_guard_user->getIsActive() == 1 && $this->sf_guard_user->getUserprofile()->getUserprofileValidate() == null) $this->redirect($request->getReferer());
    //    return $this->renderText($user->getId());
    //$aktivny = $this->sf_guard_user->getIsActive() = 1 ? "ano" : "nie";
    if($this->sf_guard_user->getIsActive() == 1):
      $aktivny = "aktívny";
    else:
      $aktivny = "neaktívny";
    endif;
    return $this->renderText($this->sf_guard_user->getUserprofile()->getFullname()."<br> - ".$aktivny."<br> - ".$this->sf_guard_user->getUserprofile()->getUserprofileValidate()); // = return $this->renderText($this->sf_guard_user->Userprofile->getUserprofileValidate());
//    return $this->renderText($this->sf_guard_user->getUserprofile()->getFullname()."<br> - ".$this->sf_guard_user->getIsActive() ? "aktívny" : "neaktívny"."<br> - ".$this->sf_guard_user->getUserprofile()->getUserprofileValidate()); // = return $this->renderText($this->sf_guard_user->Userprofile->getUserprofileValidate());
  }
*/


/*
  // zakazat akciu 'new'
  public function executeNew(sfWebRequest $request)
  {
//    return $this->forward404();
    $this->redirect($request->getReferer());
  }
*/

  // skopirovane z cache
  protected function executeBatchDelete(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');

//SM >>>
    foreach ($ids as $k => $v)
    {
      $p=Doctrine::getTable('sfGuardUser')->findOneById($v);
      if($p && (count($p->Zoologys)>0))
      {
	$msg[] = $p->getUsername();
	unset($ids[$k]);
      }
    }
    if(count($msg) > 0)
    {
      sfProjectConfiguration::getActive()->loadHelpers(array('I18N'));
      $msg = __('Objects "%1%" wasn\'t deleted, because they\'re related with another objects', array("%1%" => implode(", ", $msg))).'.';
    }
    $this->getUser()->setFlash('error', $msg);
    if(count($ids) < 1)	$this->redirect('@person');
//<<<

    $count = Doctrine_Query::create()
      ->delete()
      ->from('sfGuardUser')
      ->whereIn('id', $ids)
      ->execute();

    if ($count >= count($ids))
    {
      $this->getUser()->setFlash('notice', 'The selected items have been deleted successfully.');
    }
    else
    {
      $this->getUser()->setFlash('error', 'A problem occurs when deleting the selected items.');
    }

    $this->redirect('@sf_guard_user');
  }

  public function executeDelete(sfWebRequest $request)
  {
    // SM >>>
    sfProjectConfiguration::getActive()->loadHelpers(array('I18N'));
    // SM <<<

    $request->checkCSRFProtection();

    $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $this->getRoute()->getObject())));

    // SM >>>
    if(count($this->getRoute()->getObject()->Zoologys) > 0) // tj, ak je predmetna osoba previazana aspon s 1 rocnou spravou; vhodnou podmienkou by bolo aj 'if(count($this->getRoute()->getObject()->getRocneSpravyOsobas()) > 0)'
    {

      $this->getUser()->setFlash('error', __('The item was not deleted, because it is related with minimal 1 object of the "%1%".', array("%1%" => 'zoology')));
      $this->redirect('@sf_guard_user');
    }
    // SM <<<

    $this->getRoute()->getObject()->delete();

    $this->getUser()->setFlash('notice', 'The item was deleted successfully.');

    $this->redirect('@sf_guard_user');
  }

  // skopirovane z cache
  public function executeNew(sfWebRequest $request)
  {
    $this->form = $this->configuration->getForm();
    $this->sf_guard_user = $this->form->getObject();
//SM >>>
    //pre novovyvtoraneho uzivatela nastavujem defaultne prava write a view
    $this->form->setDefault('permissions_list', Doctrine_Query::create()->from('sfGuardPermission a')->whereIn('a.name', array('write', 'view'))->execute()->getPrimaryKeys());
//<<<
  }


  public function executeEdit(sfWebRequest $request)
  {
    $this->sf_guard_user = $this->getRoute()->getObject();
//return $this->renderText(sfContext::getInstance()->getRouting()->getCurrentInternalUri(true));
//SM >>>
    // ak je ucet neaktivny ale nema validacny kod, tak ho treba narychlo vytvorit
    if($this->sf_guard_user->getIsActive() == 0 && $this->sf_guard_user->Profile->getValidate() == null)
    {
      $guid = "n" . self::createGuid();
      $this->sf_guard_user->Profile->setValidate($guid);
      $this->sf_guard_user->Profile->save();
    }
    // ak je ucet aktivny a pritom ma aj validacny kod, tak ho treba narychlo zrusit
    if($this->sf_guard_user->getIsActive() == 1 && $this->sf_guard_user->Profile->getValidate() != null)
    {
      if(!is_null($this->sf_guard_user->getLastLogin()) &&substr($this->sf_guard_user->Profile->getValidate(), 0, 1)!='r')
      {
	$this->sf_guard_user->Profile->setValidate(null);
        $this->sf_guard_user->Profile->save();
      }
    }
//if($this->sf_guard_user->getIsActive() == 1)	      $this->sf_guard_user->setIsActive(true);
//SM <<<
    $this->form = $this->configuration->getForm($this->sf_guard_user);
  }


  // toto bude akcia na aktivovanie usera, vymaze userprofile_validate, nastavi is_active=1 a  posle predmetnemu uzivatelovi mail
  public function executeActivate(sfWebRequest $request)
  {
    $sfGuardUser = $this->getRoute()->getObject();
    $profile = $sfGuardUser->getProfile();
    if($sfGuardUser->getIsActive() == 1 && $profile->getValidate() == null)
      $this->redirect(sfContext::getInstance()->getRouting()->getCurrentInternalUri(true)); //$this->redirect($request->getReferer());

    // ulozim si povodny validacny kluc do premennej, v pripade zlyhania odoslania meilu ho vratim
    $validate = $profile->getValidate();
//    $type = self::getValidationType($validate);
/*    if (!strlen($validate))
    {
      return 'Invalid';
    }
*/
//SM, 26.03.2010 >>>
    // ak aktivujem prvy raz uzivatela, spravidla sa jedna o novozalozeneho uzivatela
    if(is_null($sfGuardUser->getLastLogin()) &&substr($profile->getValidate(), 0, 1)=='n')
    {
      $guid = "r" . self::createGuid();
      $profile->setValidate($guid);
    }
    else
    {
//SM, 26.03.2010 <<<
      $profile->setValidate(null);
//SM, 26.03.2010 >>>
    }
//SM, 26.03.2010 <<<
    $profile->save();
//    if ($type == 'New')
//    {
      $sfGuardUser->setIsActive(true);  
      $sfGuardUser->save();
//      $this->getUser()->signIn($sfGuardUser);
//    }
//>>>
        try
        {
	  // ak aktivujem prvy raz uzivatela, spravidla sa jedna o novozalozeneho uzivatela
	  if(is_null($sfGuardUser->getLastLogin()) &&substr($profile->getValidate(), 0, 1)=='r')
	  {
	    $subject = sfContext::getInstance()->getI18N()->__('This new account is activated');
	    $parameters = array('name' => $profile->getFullname(), 'user_email' => $profile->getEmail(), 'username' => $sfGuardUser->getUsername(), 'validate' => $profile->getValidate());
	    $body = 'sfGuardUser/sendCreateAndActivateToUser';
	  }
	  else
	  {
	    $subject = sfContext::getInstance()->getI18N()->__('This account is activated');
	    $parameters = array('name' => $profile->getFullname(), 'user_email' => $profile->getEmail());
	    $body = 'sfGuardUser/sendActiveToUser';
	  }

	  $this->mail(array(
	    'subject' => $subject,
            'fullname' => $profile->getFullname(),
            'email' => $profile->getEmail(),
            'parameters' => $parameters,
            'text' => $body,
            'html' => $body
	  ));
/*
)

	  {
	    $notice_msg = " ".__('E-mail to user "%1%" was send successfully', array("%1%" => $sfGuardUser->getUsername()));
	    if(isset($notice))	$notice.=$notice_msg;
	    else	$notice=$notice_msg;
	  }
	  else
	  {
	    $error_msg = " ".__('E-mail to user "%1%" was not send', array("%1%" => $sfGuardUser->getUsername()));
	    if(isset($error))	$error.=$error_msg;
	    else	$error=$error_msg;
	  }
	  if(isset($notice))	$this->getUser()->setFlash('notice', $notice);
	  if(isset($error))	$this->getUser()->setFlash('error', $error);
*/
	  $notice_msg = " ".__('E-mail to user "%1%" was send successfully', array("%1%" => $sfGuardUser->getUsername()));
	  if(isset($notice))	$notice.=$notice_msg;
	  else	$notice=$notice_msg;
	  $this->getUser()->setFlash('notice', $notice);

	  $this->redirect($request->getReferer());
        }
        catch (Exception $e)
        {
//          $mailer->disconnect();
//	  $profile->setUserprofileValidate($validate);
//	  $profile->save();
//	  $sfGuardUser->setIsActive(false);  
//	  $sfGuardUser->save();
          // You could re-throw $e here if you want to 
          // make it available for debugging purposes
//          return 'MailerError';
	  return $this->renderText("chyba v odoslani emailu");
        }
  }

  public function executeDeactivate(sfWebRequest $request)
  {
//$request->getParameter('id');
    $sfGuardUser = $this->getRoute()->getObject();
    $profile = $sfGuardUser->getProfile();
    if($sfGuardUser->getIsActive() == 0 && $profile->getValidate() != null)
      $this->redirect(sfContext::getInstance()->getRouting()->getCurrentInternalUri(true)); //$this->redirect($request->getReferer());
    if($sfGuardUser->getIsActive() == 1):
      $aktivny = "aktívny";
    else:
      $aktivny = "neaktívny";
    endif;

    $guid = "n" . self::createGuid();
    $profile->setValidate($guid);

    $profile->save();
    $sfGuardUser->setIsActive(false);
    $sfGuardUser->save();
        try
        {
	  $subject = sfContext::getInstance()->getI18N()->__('This account is deactivated');
//          if(
	  $this->mail(array(
	    'subject' => $subject,
            'fullname' => $profile->getFullname(),
            'email' => $profile->getUserprofileEmail(),
            'parameters' => array(
				  'name' => $profile->getFullname(),
				  'user_email' => $profile->getUserprofileEmail()
			    ),
            'text' => 'sendDeactiveToUser',
            'html' => 'sendDeactiveToUser'
	  ));
//)
/*	  {
	    $notice_msg = " ".__('E-mail to user "%1%" was send successfully', array("%1%" => $sfGuardUser->getUsername()));
	    if(isset($notice))	$notice.=$notice_msg;
	    else	$notice=$notice_msg;
	  }
	  else
	  {
	    $error_msg = " ".__('E-mail to user "%1%" was not send', array("%1%" => $sfGuardUser->getUsername()));
	    if(isset($error))	$error.=$error_msg;
	    else	$error=$error_msg;
	  }
	  if(isset($notice))	$this->getUser()->setFlash('notice', $notice);
	  if(isset($error))	$this->getUser()->setFlash('error', $error);
*/
	  $notice_msg = " ".__('E-mail to user "%1%" was send successfully', array("%1%" => $sfGuardUser->getUsername()));
	  if(isset($notice))	$notice.=$notice_msg;
	  else	$notice=$notice_msg;
	  $this->getUser()->setFlash('notice', $notice);

	  $this->redirect($request->getReferer());
        }
        catch (Exception $e)
        {
//          $mailer->disconnect();
//          $profile->setUserprofileValidate(null);
//          $profile->save();
//          $sfGuardUser->setIsActive(true);
//	  $sfGuardUser->save();
          return $this->renderText("chyba v odoslani emailu");
        }

  }


  // akcia prebrata zo sfApply
  static private function createGuid()
  {
    $guid = "";
    // This was 16 before, which produced a string twice as
    // long as desired. I could change the schema instead
    // to accommodate a validation code twice as big, but
    // that is completely unnecessary and would break 
    // the code of anyone upgrading from the 1.0 version.
    // Ridiculously unpasteable validation URLs are a 
    // pet peeve of mine anyway.
    for ($i = 0; ($i < 8); $i++) {
      $guid .= sprintf("%02x", mt_rand(0, 255));
    }
    return $guid;
  }

  // akcia prebrata zo sfApply
  static private function getValidationType($validate)
  {
    $t = substr($validate, 0, 1);  
    if ($t == 'n')
    {
      return 'New';
    } 
    elseif ($t == 'r')
    {
      return 'Reset';
    }
    else
    {
      return sfView::NONE;
    }
  }

  protected function mail($options) {
    $required = array('subject', 'parameters', 'email', 'fullname', 'html', 'text');
    foreach ($required as $option)
    {
      if (!isset($options[$option]))
      {
        throw new sfException("Required option $option not supplied to sfApply::mail");
      }
    }
    $message = $this->getMailer()->compose();
    $message->setSubject($options['subject']);
    
    // Render message parts
    $message->setBody($this->getPartial($options['html'], $options['parameters']), 'text/html');
    $message->addPart($this->getPartial($options['text'], $options['parameters']), 'text/plain');
    $address = $this->getFromAddress();
    $message->setFrom(array($address['email'] => $address['fullname']));
    $message->setTo(array($options['email'] => $options['fullname']));
    $this->getMailer()->send($message);
  }

  protected function getFromAddress()
  {
    $from = sfConfig::get('app_sfApplyPlugin_from', false);
    if (!$from)
    {
      throw new Exception('app_sfApplyPlugin_from is not set');
    }
    // i18n the full name
    return array('email' => $from['email'], 'fullname' => sfContext::getInstance()->getI18N()->__($from['fullname']));
  }



/*
  public function executeUpdate(sfWebRequest $request)
  {
    $this->sf_guard_user = $this->getRoute()->getObject();
    $this->form = $this->configuration->getForm($this->sf_guard_user);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }
*/

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? __('The item was created successfully')."." : __('The item was updated successfully').".";
//SM >>>
      $form->getObject()->isNew() ? $form->getObject()->setCreatedAt(date('Y-m-d H:i:s')) : $form->getObject()->setUpdatedAt(date('Y-m-d H:i:s'));
      $form->getObject()->isNew() ? $form->getObject()->getProfile()->setCreatedAt(date('Y-m-d H:i:s')) : $form->getObject()->getProfile()->setUpdatedAt(date('Y-m-d H:i:s'));

      $new = false;
      if($form->getObject()->isNew())
      {
        $guid = "n" . self::createGuid();
        $form->getObject()->Profile->setValidate($guid);
	$new = true;
      }
//SM <<<

      $sf_guard_user = $form->save();
// ak je vytvoreny adminom novy uzivatel, nech je na jeho mail poslany mail s prihlas. udajmi
      if($new == true)
      {
//SM, 26.03.2010, zneaktivnujem odosielanie mailu po vytvoreni uzivatela >>>
/*
	if($this->sendEmailToUserAccountCreated($form))
	{
	  $notice_msg = " ".__('E-mail to user "%1%" was send successfully', array("%1%" => $form->getObject()->getUsername()));
	  if(isset($notice))	$notice.=$notice_msg;
	  else	$notice=$notice_msg;
	}
	else
	{
	  $error_msg = " ".__('E-mail to user "%1%" was not send', array("%1%" => $form->getObject()->getUsername()));
	  if(isset($error))	$error.=$error_msg;
	  else	$error=$error_msg;
	}
*/
      }
      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $sf_guard_user)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@sf_guard_user_new');
      }
      else
      {
        if(isset($notice))	$this->getUser()->setFlash('notice', $notice);
	if(isset($error))	$this->getUser()->setFlash('error', $error);

        $this->redirect(array('sf_route' => 'sf_guard_user_edit', 'sf_subject' => $sf_guard_user));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

  private function sendEmailToUserAccountCreated($form)
  {
	try
        {
	  $subject = sfContext::getInstance()->getI18N()->__("This account is created on %1%", array("%1%" => $this->getRequest()->getHost()));

	  $this->mail(array(
	    'subject' => $subject,
            'fullname' => $form->getObject()->getProfile()->getFullname(),
            'email' => $form->getObject()->getProfile()->getEmail(),
            'parameters' => array(
				  'name' => $form->getObject()->getProfile()->getFullname(),
				  'user_email' => $form->getObject()->getProfile()->getEmail(),
				  'username' => $form->getObject()->getUsername()
			    ),
            'text' => 'sfGuardUser/sendCreateToUser',
            'html' => 'sfGuardUser/sendCreateToUser'
	  ));
	  return true;
        }
        catch (Exception $e)
        {
          return false;
        }

  }

  
  //fill object $records with data, if filtered by user or all data of user, inspired by function ..nestlocalities/Csvexport
  //exportuj aj filtrovanu sadu do csv formatu
  public function executeCsvexportrecusers(sfWebRequest $request)
  {
    sfProjectConfiguration::getActive()->loadHelpers(array('I18N'));

    if($this->getFilters() != null):

      $this->filters = $this->configuration->getFilterForm($this->getFilters());

      $query = $this->filters->buildQuery($this->getFilters());
      $query->removeDqlQueryPart('orderby');

      $records = $query->execute();

    else:

      $records = Doctrine_Core::getTable('sfGuardUser')->findAll();

    endif;
    $iPomHranica = 1500;
    if($records->count() > $iPomHranica):
      $this->getUser()->setFlash('notice','Príliš veľa riadkov pre export: '.$records->count().' hranica je nastavená na: '.$iPomHranica);
      $this->redirect('@sf_guard_user');
    else: 

    //inspired by other excel export examples in system
    $excel_subor = '/tmp/dummy_'.$this->getUser()->getGuardUser()->getId().'_'.date('Ymd');
    $fp = fopen($excel_subor, 'w');

    //field names definition for csv export file,
    //example of conversion from utf8 to windows : $polia_nazvy[] = iconv('UTF-8', 'cp1250', __('Id'));
    $polia_nazvy[] = __('Username');
    $polia_nazvy[] = __('User profile');
    $polia_nazvy[] = __('Groups');
    $polia_nazvy[] = __('Permissions');

    // example of tabulator as delimiter (http://www.asciitable.com/)
    if(isset($fp))       fputcsv($fp, $polia_nazvy, chr(011)); 

    $aData = array();
    foreach($records as $row):

        $aData[] = $row->getUsername();
        $aData[] = $row->getProfile();

        $aPom = '';
        $iPom = 0;
        $iPomMax = $row->getGroups()->count();

        foreach($row->getGroups() as $gr)
        { 
          $aPom .= $gr->getName();
          if($iPom < $iPomMax - 1) $aPom .= PHP_EOL; 
          $iPom++;
        };
        $aData[] = $aPom;        


        $aPom = '';
        $iPom = 0;
        $iPomMax = $row->getPermissions()->count();

        foreach($row->getPermissions() as $gr)
        { 
          $aPom .= $gr->getName();
          if($iPom < $iPomMax - 1) $aPom .= PHP_EOL; 
          $iPom++;
        };
        $aData[] = $aPom;



        if(isset($fp)) fputcsv($fp, $aData, chr(011)); // ak chcem ako oddelovac poli pouzit tabulator (http://www.asciitable.com/)
        unset($aData);
    endforeach;

    if(isset($fp))
    {
      fclose($fp);
    }

    $response = $this->getContext()->getResponse();
    $response->setHttpHeader("Cache-Control", "no-store, no-cache, must-revalidate");
    $response->setHttpHeader("Cache-Control", "post-check=0, pre-check=0");
    $response->setHttpHeader('Content-Type', 'application/force-download');
    $response->setHttpHeader('Content-Type', 'application/download');
    $response->setHttpHeader('Expires', gmdate('D, d M Y H:i:s') . ' GMT');
    $response->setHttpHeader('Pragma', 'public');
    $response->setHttpHeader('Pragma', 'no-cache');

    if(isset($fp))
    {
      $response->setHttpHeader('Content-Type', 'application/vnd.ms-excel; charset=windows-1250');
      $response->setHttpHeader('Content-Disposition', 'attachment; filename="export_records_'.date('Ymd').'.xls"');
    }

    $response->setContent(file_get_contents($excel_subor, 'rb'));

    unlink($excel_subor);
    $this->setLayout(false);
    return sfView::NONE;

    endif; 

  }





}

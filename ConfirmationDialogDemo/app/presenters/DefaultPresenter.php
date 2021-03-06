<?php

/**
 * Confirmation dialog with dynamic signals
 * Example presenter
 *
 * Copyright (c) 2009 Lukáš Doležal @ GDMT (dolezal@gdmt.cz)
 *
 * This source file is subject to the "Creative Commons CC-BY 3.0" license
 *
 * @copyright  Copyright (c) 2009 Lukáš Doležal (dolezal@gdmt.cz)
 * @license    http://creativecommons.org/licenses/by/3.0  Creative Commons CC-BY
 * @link       http://nettephp.com/cs/extras/confirmation-dialog
 * @package    ConfirmationDialog
 * @subpackage Example
 */


use Nette\Application\UI\Presenter;
use Nette\Utils\Html;

// You should move ConfirmationDialog folder from the distribution to your 
// libs folder but in this demo we don't want to distribute the addon twice.
include __DIR__ . '/../../../ConfirmationDialog/ConfirmationDialog.php';


class DefaultPresenter extends Presenter
{

	/** ConfirmationDialog factory */
	public function createComponentConfirmForm()
	{
		$form = new ConfirmationDialog($this->context->session->getSection('ConfirmationDialog/tokens'));

		// you can easily create AJAX confirm form with eg. jquery.ajaxforms.js
		$form->getFormElementPrototype()->addClass('ajax');
		$form->dialogClass = 'static_dialog';

		// create dynamic signal for 'confirmDelete!'
		$form->addConfirmer(
			'delete',
			array($this, 'confirmedDelete'),
			function ($dialog, $params) {
				return sprintf('Do you really want to delete user \'%s\'?', $params['id']);
			});

		$form->addConfirmer(
			'deleteRecursive',
			array($this, 'confirmedDeleteRecursive'),
			function ($dialog, $params) {
				// change class of question element
				$dialog->dialogClass .= ' important';
				return sprintf('Do you really want to delete user \'%s\' and all articles connected with him?', $params['id']);
			});

		$form->addConfirmer(
			'enable',
			array($this, 'confirmedEnable'),
			function ($dialog, $params) {
				return sprintf('Do you really want to enable user \'%s\'?', $params['id']);
			});

		$form->addConfirmer(
			'infinite',
			array($this, 'confirmedInfinite'),
			function ($dialog, $params) {
				return sprintf('Infinite dialog. You are at step \'%s\' Do you want to go to next step?', $params['num']);
			});

		return $form;
	}


	public function createComponentNonajaxForm()
	{
		$form = new ConfirmationDialog($this->context->session->getSection('ConfirmationDialog/tokens'));

		$form->dialogClass = 'static_dialog second';
		$form->getFormButton('yes')->getControlPrototype()->addClass('yesbut');
		$form->getFormButton('no')->getControlPrototype()->addClass('nobut');

		// create dynamic signal for 'confirmDelete!'
		$form->addConfirmer(
			'delete',
			array($this, 'confirmedDelete'),
			function ($dialog, $params) {
				return sprintf('Do you really want to delete user \'%s\'?', $params['id']);
			});

		$form->addConfirmer(
			'deleteRecursive',
			array($this, 'confirmedDeleteRecursive'),
			function ($dialog, $params) {
				// change class of question element
				$dialog->dialogClass .= ' important';
				return sprintf('Do you really want to delete user \'%s\' and all articles connected with him?', $params['id']);
			});

		$form->addConfirmer(
			'enable',
			array($this, 'confirmedEnable'),
			function ($dialog, $params) {
				return sprintf('Do you really want to enable user \'%s\'?', $params['id']);
			});

		$form->addConfirmer(
			'infinite',
			array($this, 'confirmedInfinite'),
			function ($dialog, $params) {
				$el = Html::el();
				$el->setHtml(
					sprintf('<big>Infinite dialog.</big><br />You are at step \'%s\'. Do you want to go to next step?', $params['num'])
				);
				return $el;
			});

		return $form;
	}


	/*********** signal processing ***********/

	function confirmedEnable($id)
	{
		$this->flashMessage('User enabled.');

		if (!$this->isAjax())
			$this->redirect('this');
	}

	function confirmedDeleteRecursive($id)
	{
		$this->flashMessage('User completely deleted');

		if (!$this->isAjax())
			$this->redirect('this');

	}


	function confirmedDelete($id, $dialog)
	{
		$this->flashMessage(
			'Cannot delete user due to some dependencies.',
			'error'
		);

		// show aditional confirmation question
		$dialog->showConfirm('deleteRecursive', array('id' => $id));
	}


	function confirmedInfinite($num, $dialog)
	{
		// show aditional confirmation question
		$dialog->showConfirm('infinite', array('num' => $num + 1));
		return;
	}

	function renderDefault()
	{
		$this->template->showAjaxLinks = !$this['confirmForm']->isVisible();
		$this->invalidateControl('links');
		$this->invalidateControl('flashes');
	}

} 

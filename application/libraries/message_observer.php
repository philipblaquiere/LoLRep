<?php

interface IObserver
{
  function onChanged( $sender, $args);
}

interface IObservable
{
  function addObserver ( $observer);
}

class Thread implements IObservable
{

  public function addPlayertoThread($player)
  {
    foreach ($this->_observers as $obs) {
      $obs -> onChanged ($this, $player);
    }
  }
  public function newMessage($from, $message_body)
  {

    foreach ($this->_observers as $obs) {
      $obs -> onNewMessage ($this, $from, $message_body);
    }

  }
  public function addObserver($observer)
  {
    $this->_observers[] = $observer;
  }
}

class ThreadLogger implements IObserver
{
  public function onChanged ($sender, $player)
  {
    echo ( "'$player' has joined the conversation\n");
  }

  public function onNewMessage($sender, $from, $message_body)

  {
      //do some shit here

    echo ( "'$from' said ' $message_body'\n");

  }
}

$thread = new Thread();
$thread->addObserver( new ThreadLogger() );
$thread->addPlayertoThread( "Jack" );
$thread->addPlayertoThread( "maromarius" );
$thread->newMessage("maromarius", "yo new message");
$thread->newMessage("Jack", "hey man");



?>

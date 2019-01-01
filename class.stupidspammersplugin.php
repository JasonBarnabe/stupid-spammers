<?php
class StupidSpammersPlugin extends Gdn_Plugin {
  public function PostController_BeforeFormButtons_Handler($Sender) {
    // Only new discussions
    if ($Sender->Discussion) {
      return;
    }
    if (!$this->requiresChallenge()) {
      return;
    }
    echo '<div class="P">'.$this->challengeQuestion().'<input name="challenge_answer" required autocomplete=off size=3></div>';
  }
  
  public function DiscussionModel_BeforeSaveDiscussion_Handler($sender) {
    // Only new discussions
    if ($sender->EventArguments['DiscussionID']) {
      return;
    }
    if (!$this->requiresChallenge()) {
      return;
    }
    if ($sender->EventArguments['FormPostValues']['challenge_answer'] != $this->challengeAnswer()) {
      $sender->Validation->addValidationResult('Question', "Answer is incorrect.");
    }
  }
  
  private function requiresChallenge() {
    return Gdn::session()->User->CountDiscussions < 5;
  }
  
  private function challengeQuestion() {
    return implode(" + ", $this->challengeUserIdPart()) . " + " . $this->challengeDiscussionCountPart() . " = ";
  }
  
  private function challengeAnswer() {
    return $this->challengeDiscussionCountPart() + array_sum($this->challengeUserIdPart());
  }
  
  private function challengeUserIdPart() {
    $a = array_slice(str_split(Gdn::session()->UserID), -3);
    $a[count($a)-1] *= 10;
    return $a;
  }
  
  private function challengeDiscussionCountPart() {
    return ((Gdn::session()->User->CountDiscussions == null ? 0 : Gdn::session()->User->CountDiscussions) % 3 + 1) ** 2;
  }
}

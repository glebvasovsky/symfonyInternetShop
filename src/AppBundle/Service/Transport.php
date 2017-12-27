<?php

namespace AppBundle\Service;

use Swift_SmtpTransport;

class Transport extends Swift_SmtpTransport
{
    /**
     * @param string $username
     * 
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->__call('setUsername', [$username]);
        
        return $this;
    }
    
    /**
     * @param string $password
     * 
     * @return self
     */
    public function setPassword(string $password): self 
    {
        $this->__call('setPassword', [$password]);
        
        return $this;
    }
}

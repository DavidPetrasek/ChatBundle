<?php
namespace FOS\ChatBundle\Model;

enum ParticipantStatus: int
{
    case ONLINE = 1;
    case AWAY = 2;
    case BUSY = 3;
    case OFFLINE = 4;
}



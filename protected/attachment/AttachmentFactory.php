<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/11 11:49
 * Describe：
 */
class AttachmentFactory
{
    public static function getInstance($type)
    {
        switch ($type)
        {
            default:
                return new Attachment($type);
                break;
        }
    }
}
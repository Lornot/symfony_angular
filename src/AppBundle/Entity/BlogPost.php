<?php

namespace AppBundle\Entity;

use JMS\Serializer\Annotation as JMSSerializer;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\BlogPostRepository")
 * @ORM\Table(name="blog_post")
 * @JMSSerializer\ExclusionPolicy("all")
 */
class BlogPost implements \JsonSerializable
{
    /**
     * @var int
     * @JMSSerializer\Expose
     */
    private $id;

    /**
     * @var string
     * @JMSSerializer\Expose
     */
    private $title;

    /**
     * @var string
     * @JMSSerializer\Expose
     */
    private $body;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return BlogPost
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return BlogPost
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'body'  => $this->body,
        ];
    }
}


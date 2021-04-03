<?php

namespace MageArray\ProductQuestions\Controller\Qalikedislike;

class Setlike extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_objectManager = $context->getObjectManager();
        parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $collection = $this->_objectManager
            ->create('MageArray\ProductQuestions\Model\Likedislike')
            ->getCollection();
        $collection->addFieldToFilter('customer_id', $post['customerId']);
        $collection->addFieldToFilter('qa_id', $post['qaId']);
        $collection->addFieldToFilter('type', $post['typeId']);

        if (count($collection) > 0) {
            $likedislikeModelLoad = $this->_objectManager
                ->create('MageArray\ProductQuestions\Model\Likedislike');
            $likedislikeModelLoad->load(
                $collection->getFirstItem()->getLikeDislikeId()
            );
            if ($likedislikeModelLoad->getLike() == 0) {
                $likedislikeModelLoad->setLike(1);
                $likedislikeModelLoad->setDislike(0);
                $likedislikeModelLoad->save();
            } else {
                $likedislikeModelLoad->setLike(0);
                $likedislikeModelLoad->save();
            }
            
        } else {
            $likedislikeModel = $this->_objectManager
                ->create('MageArray\ProductQuestions\Model\Likedislike');
            $likedislikeModel->setQaId($post['qaId']);
            $likedislikeModel->setLike(1);
            $likedislikeModel->setCustomerId($post['customerId']);
            $likedislikeModel->setType($post['typeId']);
            $likedislikeModel->save();
        }
        $layout = $this->_view->getLayout();
        $block = $layout
            ->createBlock('MageArray\ProductQuestions\Block\QuestionAnswer');
        $like = $block->getQALikes($post['qaId'], $post['typeId']);
        $dislike = $block->getQADisLikes($post['qaId'], $post['typeId']);
        $arr = [
            'dislike' => $dislike,
            'like' => $like,
            'type' => $post['typeId']
        ];
        echo json_encode($arr);
    }
}
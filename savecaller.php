<?php

namespace MDC\LoginAsCustomerSeller\Controller\Sellerhtml\Index;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magedelight\Backend\App\Action\Context;
use MDC\CallerCustomer\Model\CallerCustomerFactory;
use Magento\Framework\DataObject;

class SaveCaller extends \Magedelight\Backend\App\Action
{
    protected $resultPageFactory;
    protected $customer;
    protected $storeManager;
    protected $scopeConfig;
    protected $transportBuilder;
    protected $customerRegistry;
    protected $dataProcessor;
    protected $_customerRepository;
    protected $_mathRandom;
    protected $_accountmanagement;
    protected $collectionFactory;
    protected $createPost;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Customer\Model\AccountManagement $accountmanagement,
        \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \MDC\ConsentManagement\Model\ConsentAttributesRepository $consentAttributesRepository,
        \MDC\ConsentManagement\Model\ConsentAttributes $consentAttributesModel,
        CallerCustomerFactory $callerCustomer,
        DataObject $dataObject,
        \Magento\Framework\Url $urlHelper,
        \MDC\CallerCustomer\Controller\Sellerhtml\Account\CreatePost $createPost,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory

    ) {
        parent::__construct($context);

        $this->callerCustomer = $callerCustomer;
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->dataProcessor = $dataProcessor;
        $this->_accountmanagement = $accountmanagement;
        $this->_customerRepository = $customerRepository;
        $this->_mathRandom = $mathRandom;
        $this->customerRegistry = $customerRegistry;
        $this->senderResolver = $senderResolver ?? ObjectManager::getInstance()->get(SenderResolverInterface::class);
        $this->logger = $logger;
        $this->consentAttributesRepository=$consentAttributesRepository;
        $this->consentAttributesModel = $consentAttributesModel;
        $this->dataobject = $dataObject;
        $this->urlHelper = $urlHelper;
        $this->createPost = $createPost;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $responseData = [];
        //$this->resultJsonFactory = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $postData = $this->getRequest()->getPostValue();
        $response = $this->dataobject;
        $callerExistObj = $this->callerCustomer->create()->checkExistCallerCustomer($postData['firstname'], $postData['lastname'], $postData['postcode']);
        $callerCollection = $this->callerCustomer->create()->getCollection()->addFieldToFilter('relation_id', $callerExistObj->getId());
        if(!empty($postData)){
            if (isset($postData['is_setup_online_account'])) {
                $accountStatus = $this->createPost->isCallerOnlineNewAccountExist($postData);
                if ($accountStatus) {
                    $responseData = [
                        'status' => false,
                        'message' => __('A web account already exists for the caller ' . $postData['email']),
                        'redirect' => false
                    ];
    
                    $resultJson->setData($responseData);
                    return $resultJson;
                }
            }
            $consentData = array();
            $consentData['opt_out_all_marketing']=isset($postData['opt_out_all_marketing']) ? $postData['opt_out_all_marketing']:0;
            $consentData['mail']=isset($postData['consent_email']) ? $postData['consent_email']:0;
            $consentData['newsletter_email'] = isset($postData['newsletter']) ? $postData['newsletter'] : 0;
            $consentData['product_n_service_email'] = isset($postData['product']) ? $postData['product'] : 0;
            $consentData['telephone'] = isset($postData['telephone']) ? $postData['telephone'] :0;
            $consentData['market_pref_telephone_service'] = isset($postData['market_pref_telephone_service']) ? $postData['market_pref_telephone_service'] :0;
            $consentData['market_pref_telephone_marketing'] = isset($postData['market_pref_telephone_marketing']) ? $postData['market_pref_telephone_marketing'] :0;
            $consentData['email']=isset($postData['consent_email']) ? $postData['consent_email']:0;
            $consentData['sms']=isset($postData['sms']) ? $postData['sms']:0;
            $consentData['direct_mail']=isset($postData['direct_mail']) ? $postData['direct_mail']:0;
            $consentData['opt_out_all_correspondence']=isset($postData['opt_out_all_correspondence']) ? $postData['opt_out_all_correspondence']:0;
            if (count($callerCollection) > 0) {
                if(($callerExistObj->getData('firstname') == $postData['firstname'] && $callerExistObj->getData('lastname') == $postData['lastname'] && $callerExistObj->getData('postcode') == $postData['postcode'])) {
                    if (($callerExistObj->getData('relation_id')) && ($callerExistObj->getData('phone_no') != $postData['phone_no'] || $callerExistObj->getData('email') != $postData['email'] || $consentData['opt_out_all_marketing'])) {
                        $callerCustomer = $this->callerCustomer->create();
                        $newCallerCustomer = $callerCustomer->load($callerExistObj->getData('relation_id'));
                        $newCallerCustomer->setData('firstname',$postData['firstname']);
                        $newCallerCustomer->setData('lastname',$postData['lastname']);
                        $newCallerCustomer->setData('phone_no',$postData['phone_no']);
                        $newCallerCustomer->setData('email',$postData['email']);
                        $newCallerCustomer->save();
                        /*update caller  */
                        $callerCollection = $this->callerCustomer->load($postData['child_id']);
                        if($callerCollection){
                            $callerCollection->setData('firstname',$postData['firstname']);
                            $callerCollection->setData('lastname',$postData['lastname']);
                            $callerCollection->setData('title',$postData['title']);
                            $callerCollection->setData('phone_no',$postData['phone_no']);
                            $callerCollection->setData('alt_phone_no',$postData['alt_phone_no']);
                            $callerCollection->setData('email',$postData['email']);
                            $callerCollection->setData('update_dynamics_sync',0);
                            $callerCollection->save();
                        }
                        /*consent update */
                        $consentData['consent_attr_id'] =  $postData['consent_id'];
                        $consentModel = $this->consentAttributesRepository->get($postData['consent_id']);
                        $consentModel->setData('opt_out_all_marketing',$consentData['opt_out_all_marketing']);
                        $consentModel->setData('mail',$consentData['mail']);
                        $consentModel->setData('newsletter_email',$consentData['newsletter_email']);
                        $consentModel->setData('product_n_service_email',$consentData['product_n_service_email']);
                        $consentModel->setData('telephone',$consentData['telephone']);
                        $consentModel->setData('market_pref_telephone_service',$consentData['market_pref_telephone_service']);
                        $consentModel->setData('market_pref_telephone_marketing',$consentData['market_pref_telephone_marketing']);                
                        $consentModel->setData('email',$consentData['email']);
                        $consentModel->setData('sms',$consentData['sms']);
                        $consentModel->setData('direct_mail',$consentData['direct_mail']);
                        $consentModel->setData('user_type',2);
                        $consentModel->setData('opt_out_all_correspondence',$consentData['opt_out_all_correspondence']);
                        $consentModel->setData('sync_to_dynamics',0);
                        $this->consentAttributesRepository->save($consentModel);
                    } else {
                        $responseData = [
                            'status' => false, 
                            'message' => __('A caller already exists with the provided First letter of the First Name, Last Name and Postcode.'),
                            'redirect'=> false
                        ];
                        $resultJson->setData($responseData);
                        return $resultJson;
                    }
                }
            }
             
            $callerCollection = $this->updateCallerDetails($postData);
            if(isset($postData['is_setup_online_account'])){
                $data = [
                    'customer' =>[
                        'firstname' => $postData['firstname'],
                        'email' => $postData['email'], //customer email id
                        'lastname' => $postData['lastname'],
                        'password' => 'Admin@123',
                        'title'=>$postData['title'],
                        'postcode'=>$postData['postcode'],
                        'caller_id'=>$postData['child_id']
                        ]
                ];
                $this->createCustomer($data,$callerCollection);
            }

                $consentData = array();
                $consentData['opt_out_all_marketing']=isset($postData['opt_out_all_marketing']) ? $postData['opt_out_all_marketing']:0;
                $consentData['mail']=isset($postData['consent_email']) ? $postData['consent_email']:0;
                $consentData['newsletter_email'] = isset($postData['newsletter']) ? $postData['newsletter'] : 0;
                $consentData['product_n_service_email'] = isset($postData['product']) ? $postData['product'] : 0;
                $consentData['telephone'] = isset($postData['telephone']) ? $postData['telephone'] :0;
                $consentData['market_pref_telephone_service'] = isset($postData['market_pref_telephone_service']) ? $postData['market_pref_telephone_service'] :0;
                $consentData['market_pref_telephone_marketing'] = isset($postData['market_pref_telephone_marketing']) ? $postData['market_pref_telephone_marketing'] :0;
                $consentData['email']=isset($postData['consent_email']) ? $postData['consent_email']:0;
                $consentData['sms']=isset($postData['sms']) ? $postData['sms']:0;
                $consentData['direct_mail']=isset($postData['direct_mail']) ? $postData['direct_mail']:0;
                $consentData['opt_out_all_correspondence']=isset($postData['opt_out_all_correspondence']) ? $postData['opt_out_all_correspondence']:0;

            if (isset($postData['consent_id']) && $postData['consent_id']!="") {
                $consentData['consent_attr_id'] =  $postData['consent_id'];
                $consentModel = $this->consentAttributesRepository->get($postData['consent_id']);
                $consentModel->setData('opt_out_all_marketing',$consentData['opt_out_all_marketing']);
                $consentModel->setData('mail',$consentData['mail']);
                $consentModel->setData('newsletter_email',$consentData['newsletter_email']);
                $consentModel->setData('product_n_service_email',$consentData['product_n_service_email']);
                $consentModel->setData('telephone',$consentData['telephone']);
                $consentModel->setData('market_pref_telephone_service',$consentData['market_pref_telephone_service']);
                $consentModel->setData('market_pref_telephone_marketing',$consentData['market_pref_telephone_marketing']);                
                $consentModel->setData('email',$consentData['email']);
                $consentModel->setData('sms',$consentData['sms']);
                $consentModel->setData('direct_mail',$consentData['direct_mail']);
                $consentModel->setData('user_type',2);
                $consentModel->setData('opt_out_all_correspondence',$consentData['opt_out_all_correspondence']);
                $consentModel->setData('sync_to_dynamics',0);
                $this->consentAttributesRepository->save($consentModel);
            }else{
                $consentModel = $this->consentAttributesModel;
                $consentModel->setData('opt_out_all_marketing',$consentData['opt_out_all_marketing']);
                $consentModel->setData('mail',$consentData['mail']);
                $consentModel->setData('newsletter_email',$consentData['newsletter_email']);
                $consentModel->setData('product_n_service_email',$consentData['product_n_service_email']);
                $consentModel->setData('telephone',$consentData['telephone']);
                $consentModel->setData('email',$consentData['email']);
                $consentModel->setData('sms',$consentData['sms']);
                $consentModel->setData('direct_mail',$consentData['direct_mail']);
                $consentModel->setData('sync_to_dynamics',0);
                $consentModel->setData('user_type',2);
                $consentModel->setData('opt_out_all_correspondence',$consentData['opt_out_all_correspondence']);
                $consentCollection = $this->consentAttributesRepository->save($consentModel);
                $consentId = $consentCollection->getData('consent_attr_id');
                $callerCollection->setData('consent_id',$consentId);
                $callerCollection->save();
            }
        }
        //$response->setData(['result' => 1]);
        //return $this->resultJsonFactory->setJsonData($response->toJson());
        
    }

    public function updateCallerDetails($postData){
        $callerCustomer = $this->callerCustomer->load($postData['child_id']);

        if(isset($postData['title'])){
            $callerCustomer->setTitle($postData['title']);
        }
        if(isset($postData['firstname'])){
            $callerCustomer->setFirstname($postData['firstname']);
        }
        if(isset($postData['lastname'])){
            $callerCustomer->setLastname($postData['lastname']);
        }
        if(isset($postData['phone_no'])){
            $callerCustomer->setPhoneNo($postData['phone_no']);
        }
        if(isset($postData['alt_phone_no'])){
            $callerCustomer->setAltPhoneNo($postData['alt_phone_no']);
        }
        if(isset($postData['email'])){
            $callerCustomer->setEmail($postData['email']);
        }
        if(isset($postData['postcode'])){
            $callerCustomer->setPostcode($postData['postcode']);
        }

        $callerCustomer->save();

        return $callerCustomer;
    }

    public function createCustomer($data, $callerCollection)
    {
        $emailTemplate = "custom_reset_password";
        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        if (isset($data['customer']['email'])) {
            $checkCustomer = $this->customer->create()->setWebsiteId($websiteId)->loadByEmail($data['customer']['email']);
        } else{
            $checkCustomer = $this->customer->create();
        }
        if (!$checkCustomer->hasData()) {
            try {
                $createCustomer = $this->customer->create();
                $createCustomer->setWebsiteId($websiteId);
                $createCustomer->setEmail($data['customer']['email']);
                $createCustomer->setFirstname($data['customer']['firstname']);
                $createCustomer->setLastname($data['customer']['lastname']);
                $createCustomer->setTitle($data['customer']['title']);
                $createCustomer->setPostcode($data['customer']['postcode']);
                $createCustomer->save();
                /*$customerId = $createCustomer->getId();
                $callerCollection->setData('customer_id',$customerId);
                $callerCollection->save();*/

                /* Send Email TO Customer */
                $emailSendStatus = $this->sendEmailToCustomer($createCustomer, $emailTemplate,$data['customer']['caller_id']);

            } catch (Exception $e) {
                $this->logger->info('Something went wrong when creating customer.');
            }
        } else {
            $this->logger->info('Customer Already Created.');
        }
    }

    public function getStoreId($websiteId)
    {
        return $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
    }

    public function sendEmailToCustomer($customer, $emailTemplate,$caller_id=NULL)
    {
        $storeId = $this->getStoreId($customer->getWebsiteId());
        $customerEmailData = $this->getFullCustomerObject($customer);
        $templateParams = [];
        $templateParams['email'] = $this->getFrontendUrlWithEmail($customer->getEmail(),$this->storeManager->getStore($storeId));
        $templateParams['customer'] = $customerEmailData;
        $templateParams['store'] = $this->storeManager->getStore($storeId);
        $templateParams['caller_id'] = $caller_id;
        
        $sender = \Magento\Customer\Model\EmailNotification::XML_PATH_FORGOT_EMAIL_IDENTITY;

        $from = $this->senderResolver->resolve(
            $this->scopeConfig->getValue($sender, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId),
            $storeId
        );

        $customerName = $customer->getFirstname() . " " . $customer->getLastname();

        try {
            $transport = $this->transportBuilder->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars($templateParams)
                ->setFrom($from)
                ->addTo($customer->getEmail(), $customerName)
                ->getTransport();
            $transport->sendMessage();
            $this->logger->info($customer->getEmail() . ' : Email Send Successfully.');
        } catch (Exception $e) {
            $this->logger->error($customer->getEmail() . ' : Something went wrong when sending email');
        }
    }

    public function getFullCustomerObject($customer)
    {
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $title = '';
        if ((strtolower($customer->getTitle()) == strtolower('Please select')) || (strtolower($customer->getTitle()) == strtolower('Title'))) {
            $title = '' ;
        }
        else {
            $title = $customer->getTitle();
        }
        $customerName = $title . " " . $customer->getLastname();
        $mergedCustomerData->setData('name', $customerName);
        $tokenVal = $this->getToken($customer->getEmail(), $customer->getWebsiteId());
        $mergedCustomerData->setData('rp_token', $tokenVal);
        $mergedCustomerData->setData('id', $customer->getId());
        return $mergedCustomerData;
    }

    public function getToken($email, $websiteId)
    {
        $customer = $this->_customerRepository->get($email, $websiteId);
        $newPasswordToken = $this->_mathRandom->getUniqueHash();
        $this->_accountmanagement->changeResetPasswordLinkToken($customer, $newPasswordToken);
        return $newPasswordToken;
    }

    public function getFrontendUrlWithEmail($email,$storeId)
    {
        return $this->urlHelper->getUrl('newsletter/Subscriber/Unsubscribe', [ '_scope' => $storeId, 'email' => $email, '_nosid' => true ]);
    }

}

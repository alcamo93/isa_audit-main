<?php

namespace App\Classes;

class StatusConstants
{
    /**
     * Status generales
     */
    const ACTIVE = 1;
    const INACTIVE = 2;
    /**
     * Status message
     */
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'error';
    const INFO = 'info';
    const DUPLICATE = 'duplicate';
    /**
     * Owner
     */
    const OWNER_ACTIVE = 1;
    const OWNER_INACTIVE = 0;
    /**
     * Status aplicability
     */
    const NOT_CLASSIFIED = 3;
    const CLASSIFIED = 4;
    const EVALUATING = 5;
    CONST FINISHED = 6;
    /**
     * Values type address
     */
    const PHYSICAL = 1;
    const FISCAL = 0;
    /**
     * Status aplicability
     */
    const NOT_AUDITED = 7;
    const AUDITED = 9;
    const AUDITING = 8;
    const FINISHED_AUDIT = 10;
    /**
     * Values application type
     */
    const FEDERAL = 1;
    const STATE = 2;
    const NO_APPLICABLE = 3;
    const LOCAL = 4;
    const SPECIFIC_APP = 8;
    /**
     * Values questions types
     */
    const FEDERAL_IDENTIFICATION_Q = 1;
    const STATE_IDENTIFICATION_Q = 2;
    const UNLOCK_QUESTION = 3;
    const SPECIAL_QUESTION = 4;
    const LOCAL_IDENTIFICATION_Q = 4;
    /**
     * Values requiremnts type
     */
    // Whith relation to questions
    const FEDERAL_IDENTIFICATION_R = 1;
    const STATE_IDENTIFICATION_R = 2;
    const LOCAL_IDENTIFICATION = 13;
    const COMPOSITE_REQUIREMENTS_IDENTIFICATION = 17; 
    // Whithout relation to questions
    const STATE_REQUIREMENT = 4;
    const LOCAL_REQUIREMENT = 12;
    const COMPOSITE_REQUIREMENTS = 5;
    // Whith relation to corporate
    const SPECIFIC_REQUIREMENT = 11;
    // No use
    const UNLOCK_REQUIREMENT = 3;
    /**
     * Flags
     */
    const FLAG_ACTIVE = 1;
    const FLAG_INACTIVE = 0;
    // /**
    //  * Status group four task
    //  */
    // const NO_MADE = 11;
    // const MADE = 12;
    /**
     * Status group five
     */
    const NO_TASK = 14;
    const REJECTED = 19;
    const NO_REALIZED = 15;
    
    const UNASSIGNED_AP = 13;
    const PROGRESS_AP = 16;
    const COMPLETED_AP = 17;
    const REVIEW_AP = 18;
    const EXPIRED_AP = 25;
    const CLOSED_AP = 27;
    
    /**
     * Status group six
     */
    const NO_RESPONSIBLE_O = 20;
    const REVIEW_O = 21;
    const EXPIRED = 22;
    const IN_FORCE = 23;
    const REJECTED_O = 24;
    /**
     * Corporates types
     */
    const CORPORATE_OPERATIVE = 0;
    const CORPORATE_NEW = 1;
    /**
     * Notifications
     */
    const REQUIREMENTS_AP = 'requirementsAP';
    /**
     * Risks attributes
     */
    const RISK_ATTRIBUTES = 3;
    const RISK_PROBABILITY = ['id_risk_attribute' => 1, 'risk_attribute' => 'Probabilidad'];
    const RISK_EXHIBITIONS = ['id_risk_attribute' => 2, 'risk_attribute' => 'Exposición'];
    const RISK_CONSEQUENCES = ['id_risk_attribute' => 3, 'risk_attribute' => 'Consecuencia'];
    /**
     * Risk level interpretation
     */
    const LOW = 'Bajo';
    const MEDIUM = 'Medio';
    const HIGH = 'Alto';
    /**
     * Conditions
     */
    const CRITICAL = 1;
    const OPERATIVE = 2;
    /**
     * Scope
     */
    const LEADER = 1;
    const NO_LEADER = 0;
    /**
     * soruce librery
     */
    const SOURCE_LIBRARY = [
        'none' => ['id' => 1, 'directory' => 'library'],
        'action_plant' => ['id' => 2, 'directory' => 'action_plant'],
        'obligations' => ['id' => 3, 'directory' => 'oblitagions'],
    ];
    /**
     * Obligation type
     */
    const OBLIGATION = 1;
    const PERMIT = 2;
    /**
     * Evidences type
     */
    const SPECIFIC = 4;
    /**
     * Answers values 
     */
    const AFFIRMATIVE = 1;
    const NEGATIVE = 2;
    /**
     * Allow multiple answer 
     */
    const ALLOW_MULTIPLE_ANSWERS = 1;
    const NOT_ALLOW_MULTIPLE_ANSWERS = 0;
}

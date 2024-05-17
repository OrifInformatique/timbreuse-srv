<?php
return [
    'monday' => 'lundi',
    'tuesday' => 'mardi',
    'wednesday' => 'mercredi',
    'thursday' => 'jeudi',
    'friday' => 'vendredi',
    'rowMorning' =>  '07:00 à 12:30',
    'rowAfternoon' =>  '12:30 à 17:45',
    'hourly' => 'horaire',
    'firstEntry' => 'première entrée',
    'lastOuting' => 'dernière sortie',
    'time' => 'temps',
    'week' => 'semaine',
    'enter' => 'entrée',
    'exit' => 'sortie',
    'hour' => 'heure',
    'enter/exit' => 'entrée/sortie',
    'timeTotal' => 'temps total',
    'day' => 'jour',
    'week' => 'semaine',
    'month' => 'mois',
    'today' => 'aujourd’hui',
    'all' => 'tout',
    'weekTime' => 'temps total de la semaine',
    'delete' => 'supprimer',
    'confirm' => 'confirmer',
    'addAccess' => 'Voulez-vous ajouter l’autorisation au compte site web « %s » de contrôler les pointages du compte pointeuse « %s » ?',
    'deleteAccess' => 'Voulez-vous supprimer l’autorisation du compte site web « %s » de contrôler les pointages du compte pointeuse « %s » ?',
    'add' => 'ajouter',
    'ci_users_list_title' => 'Accès au compte pointeuse « %s »',
    'username' => 'nom d’utilisateur',
    'id_site' => 'id site',
    'access' => 'accès',
    'yes' => 'oui',
    'no' => 'non',
    'id' => 'id',
    'name' => 'prénom',
    'surname' => 'nom',
    'ciUsername' => 'nom d’utilisateur du compte site web',
    'modifyDate' => 'date de modification',
    'back' => 'retour',
    'modifyTime' => 'temps modifié',
    'modify' => 'modifier',
    'modified' => 'modifié',
    'msgAsterisk' => '✱Données saisies ou modifiées sur le site web',
    'siteData' => 'données du site',
    'new_record' => 'nouveau pointage',
    'record' => 'pointer',
    'confirmDelete' => 'Êtes-vous sûr·e de vouloir supprimer ce pointage.',
    'badgeId' => 'Numéro du badge',
    'userId' => 'Numéro d\'utilisateur',
    'idLog' => 'Identifiant du pointage',
    'badgeDate' => 'date de pointage physique sur une pointeuse',
    'deleteDate' => 'date de suppression',
    'recordModification' => 'Modification du pointage',
    'confirmRestore' => 'Êtes-vous sûr·e de vouloir restorer ce pointage.',
    'restore' => 'restaurer',
    'timUsers' => 'utilisateurs de la pointeuse',
    'webUsers' => 'utilisateurs du site web',
    'users' => 'Utilisateurs',
    'details' => 'détails',
    'deleted' => 'supprimé',
    'timUserEdit' => 'Édition utilisateur de la pointeuse',
    'siteAccountLabel' => 'modifier le lien compte site web ⇋ compte pointeuse',
    'allocationBadgeLabel' => 'modifier l’attribution des badges',
    'siteStatus' => 'site',
    'badges' => 'badges',
    'erase' => 'effacer',
    'edit_badge' => 'modifier attribution du badge ID %s.',
    'dealloc' => 'désattribuer',
    'badgesList' => 'Badges',
    'timUsersList' => 'Utilisateurs',
    'timUserRelation' => 'utilisateur lié à ce badge',
    'badge_not_available' => 'Ce numéro de badge n’est pas disponible, merci de choisir dans la liste.',
    'user_not_available' => 'Cet utilisateur n’est pas disponible, merci de choisir dans la liste.',
    'confirmDeleteBadge' => 'Ce badge est attribuer à l’utilisateur « %s %s ».',
    'confirmDeleteTimUser' => ' Cet utilisateur est attribué au badge ID « %s ».',
    'titleConfirmDeleteBadge' => 'Supprimer le badge ID %s',
    'titleconfirmDeleteTimUser' => 'Supprimer l’utilisateur « %s %s »',
    'titlePlanning' => 'planning hebdomadaire %s',
    'titleNewPlanning' => 'Nouveau planning hebdomadaire %s',
    'dueTime' => 'temps exigé',
    'offeredTime' => 'temps offert',
    'titleList' => 'liste des plannings %s',
    'dateBegin' => 'valable du',
    'dateEnd' => 'au',
    'dateColide' => 'Il y a un chevauchement entre un planning existant et les dates que vous avez saisies.',
    'dateNotBefore' => 'La date de début est après la date de fin.',
    'errorDate' => 'Une erreur est survenue avec une date.',
    'errorNoTimAccess' => 'Votre compte n\'est pas relié à un utilisateur de la timbreuse, merci de contacter un administrateur.',
    'planning' => 'planning',
    'Defaultplanning' => 'Planning par défaut',
    'confirmDeletePlanning' => 'Êtes-vous sûr·e de vouloir supprimer ce planning ?',
    'confirmPurgeDeletePlanning' => 'Êtes-vous sûr·e de vouloir supprimer définitivement ce planning ?',
    'titleConfirmDeletePlanning' => 'Supprimer le planning ID %s',
    'confirmRestorePlanning' => 'Êtes-vous sûr·e de vouloir restaurer ce planning ?',
    'titleConfirmRestorePlanning' => 'Restaurer le planning ID %s',
    'dateColideRestore' => 'Il y a un chevauchement entre un planning existant et les dates du planning à restaurer.',
    'monthDetail' => '1er au 31 du mois',
    'balance' => 'balance',
    'showDeletedPlanning' =>'Afficher les plannings supprimés',
    'workTime' =>'Temps de travail',
    'unDefineDate' => '–',
    'planningExplanation1' => '<p>Le <strong>temps exigé</strong> correspond au temps de travail attendu. (temps à l’Orif + temps aux cours)<p> '
        . '<p>Le <strong>temps offert</strong> correspond au temps qui est automatiquement ajouté à votre temps de travail, en plus vos heures timbrées. (temps des pauses + temps aux cours)</p> '
        . '<p>Exemple pour une personne à 100 %  (41 heures sur 5 jours) : </p> ',
    'planningExplanation2' => '<li>Pour une <em>journée de travail</em> à l’Orif, on devra indiquer 8:12 pour le <strong>temps exigé</strong> et on devra indiquer 0:30 pour le <strong>temps offert</strong> (deux pauses de 15 min.).</li> '
        . '<li>Pour une <em>journée de cours</em>, on devra indiquer 8:12 pour le <strong>temps exigé</strong> et également 8:12 pour le <strong>temps offert</strong>.</li> '
        . '<li>Pour un <em>jour férié</em>, on devra indiquer 00:00 pour le <strong>temps exigé</strong> et également 00:00 pour le <strong>temps offert</strong>.</li> ',
    'help' => 'aide',
    'rate' => 'taux',
    'title' => 'titre',
    'field_user_group_id' => 'Numéro du groupe d’utilisateurs',
    'field_user_sync_id' => 'Numéro de l’utilisateur timbreuse',
    'field_user_group_name' => 'Nom du groupe d’utilisateurs',
    'field_name' => 'Nom',
    'field_group_parent_name' => 'Nom du groupe parent',
    'create_user_group_title' => 'Ajouter un groupe d’utilisateurs',
    'update_user_group_title' => 'Modifier un groupe d’utilisateurs',
    'user_group_list' => 'Groupes d’utilisateurs',
    'select_parent_group' => 'Sélectionner un groupe parent',
    'select_user' => 'Sélectionner un utilisateur',
    'field_is_group_event_type' => 'Événement de groupe',
    'field_is_personal_event_type' => 'Événement personnel',
    'field_event_type_id' => 'Numéro du type d’événement',
    'field_event_date' => 'Date de l’événement',
    'field_event_start_time' => 'Heure de début de l’événement',
    'field_event_end_time' => 'Heure de fin de l’événement',
    'field_is_work_time' => 'Est-ce que cela compte comme du temps de travail ?',
    'field_is_work_time_short' => 'Temps de travail ?',
    'event_types_list' => 'Types d’événements',
    'event_type' => 'Type d’événement',
    'create_event_type_title' => 'Ajouter un type d’événement',
    'update_event_type_title' => 'Modifier un type d’événement',
    'event_type_hard_delete_explanation' => '',
    'btn_hard_delete_event_type' => 'Supprimer ce type d’événement',
    'btn_hard_delete_event_planning' => 'Supprimer cet événement',
    'btn_hard_delete_user_group' => 'Supprimer ce groupe',
    'really_want_to_delete_event_type' => 'Voulez-vous vraiment supprimer ce type d’événement ?',
    'really_want_to_delete_event_planning' => 'Voulez-vous vraiment supprimer cet événement de planning ?',
    'really_want_to_delete_user_group' => 'Voulez-vous vraiment supprimer ce groupe ?',
    'delete_event_type' => 'Supprimer le type d’événement "{event_type_name}"',
    'delete_event_planning' => 'Supprimer l’événement de planning "{event_type_name}" {of_group_or_user} "{group_or_user}"',
    'delete_user_group' => 'Supprimer le groupe d’utilisateurs "{group_name}"',
    'event_plannings_list' => 'Événements de planning',
    'field_event_date' => 'Date de l’événement',
    'field_start_time' => 'Heure de début',
    'field_end_time' => 'Heure de fin',
    'create_personal_event_planning_title' => 'Ajouter un événement personnel',
    'create_group_event_planning_title' => 'Ajouter un événement de groupe',
    'update_personal_event_planning_title' => 'Modifier un événement personnel',
    'update_group_event_planning_title' => 'Modifier un événement de groupe',
    'field_linked_user' => 'Utilisateur concerné',
    'linked_users' => 'Utilisateurs concernés',
    'field_linked_user_group' => 'Groupe concerné',
    'btn_select_linked_user' => 'Sélectionner un utilisateur',
    'select_user_group' => 'Sélectionner un groupe d’utilisateurs',
    'field_start_date' => 'Date de début',
    'field_end_date' => 'Date de fin',
    'field_recurrence_frequency' => 'Fréquence',
    'field_recurrence_interval' => 'Intervalle',
    'field_days_of_week' => 'Jours de la semaine',
    'daily' => 'Quotidien',
    'weekly' => 'Hebdomadaire',
    'monthly' => 'Mensuel',
    'btn_create_series' => 'Créer en série',
    'update_serie' => 'Modifier une série',
    'title_link_user' => 'Lien entre groupe "{group_name}" ⇋ utilisateur',
    'linked_to_group' => 'Lié au groupe',
    'cannot_delete_group_has_linked' => 'Ce groupe d’utilisateurs ne peut pas être supprimé car il est lié à d’autres éléments.',
    'cannot_delete_event_type_has_linked' => 'Ce type d’événement ne peut pas être supprimé car il est lié à au moins un événement de planning.',
    'btn_add_or_delete' => 'Ajouter / supprimer',
    'event_series_list' => 'Séries d’événements',
    'group_or_user_name' => 'Groupe / utilisateur',
    'personal_event_plannings_list' => 'Événements de planning de "{firstname} {lastname}"',
    'delete_event_serie' => 'Supprimer la série d’événements "{event_type_name}" {of_group_or_user} "{group_or_user}"',
    'btn_hard_delete_event_serie' => 'Supprimer cette série d’événements',
    'really_want_to_delete_event_serie' => 'Voulez-vous vraiment supprimer cette série d’événements ?',
    'delete_event_serie_explanation' => nl2br("La suppression d’une série d’événements va également définitivement supprimer tous les événements de planning qui lui sont liés.\n\nLes événements de planning ayant été modifiés seront eux-aussi supprimés."),
    'of_group' => 'du groupe',
    'of_user' => 'de l’utilisateur',
    'modify_occurrence' => 'Modifier l’occurrence',
    'modify_serie' => 'Modifier la série',
    'delete_occurrence' => 'Supprimer l’occurrence',
    'delete_serie' => 'Supprimer la série',
    'modify_or_delete_occurrence_or_serie' => 'Voulez-vous {update_or_delete} l’occurence ou la série ?',
    'event_part_of_serie' => 'L’événement "{event_type_name}" {of_group_or_user} "{group_or_user}" fait partie d’une série',
    'really_want_to_delete' => 'Voulez-vous vraiment supprimer cet utilisateur ?',
    'hard_delete_explanation' => 'Toutes ses données seront effacées.',
    'btn_hard_delete_user' => 'Supprimer définitivement cet utilisateur',
    'siteAccountNotLinked' => 'Ce compte utilisateur de la timbreuse n\'est pas lié à un compte de l\'application web.',
    'fillFieldsToCreateAccount' => 'Complétez les champs ci-dessous et enregistrez pour lui créer un compte dans l\'application.',
    'msg_err_end_time_greater_than' => 'L\'heure de fin doit être supérieure à l\'heure de début.',
    'msg_err_end_date_greater_than' => 'La date de fin doit être supérieure à la date de début.',
];

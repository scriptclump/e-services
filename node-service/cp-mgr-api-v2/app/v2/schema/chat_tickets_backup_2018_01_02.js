/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('chat_tickets_backup_2018_01_02', {
    fid: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    main_ticket_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    parent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    ticket_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    feedback_group_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    feedback_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    message_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    comments: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    picture: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    assigned_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    assigned_to: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    read_json: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    ticket_status: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    update_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'chat_tickets_backup_2018_01_02'
  });
};

/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('customer_feedback', {
    fid: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    main_ticket_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    legal_entity_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    parent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    ticket_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    feedback_group_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    feedback_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    comments: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    picture: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    message_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    audio: {
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
      type: DataTypes.STRING(255),
      allowNull: true
    },
    update_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'customer_feedback'
  });
};

/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('user_preferences_del_proc', {
    preference_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    preference_name: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    preference_value: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    preference_value1: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    business_start_time: {
      type: DataTypes.TIME,
      allowNull: false
    },
    business_end_time: {
      type: DataTypes.TIME,
      allowNull: false
    },
    sms_subscription: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    email_subscription: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    create_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'user_preferences_del_proc'
  });
};

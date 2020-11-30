/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('device_details', {
    device_details_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    device_id: {
      type: DataTypes.STRING(16),
      allowNull: false,
      unique: true
    },
    registration_id: {
      type: DataTypes.STRING(450),
      allowNull: true
    },
    platform_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    app_id: {
      type: DataTypes.STRING(10),
      allowNull: false
    },
    ip_address: {
      type: DataTypes.STRING(20),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    last_used_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'device_details'
  });
};

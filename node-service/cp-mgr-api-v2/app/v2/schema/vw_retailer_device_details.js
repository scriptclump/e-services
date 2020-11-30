/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_retailer_device_details', {
    firstname: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    mobile_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    device_id: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    registration_id: {
      type: DataTypes.STRING(450),
      allowNull: true
    },
    b_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    pjp_pincode_area_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    beat_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    }
  }, {
    tableName: 'vw_retailer_device_details'
  });
};

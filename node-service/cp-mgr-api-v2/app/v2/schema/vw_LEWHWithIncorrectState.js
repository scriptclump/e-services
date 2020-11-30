/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_LEWHWithIncorrectState', {
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    dc_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_code: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    lp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    lp_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    contact_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    phone_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    email: {
      type: DataTypes.STRING(96),
      allowNull: false
    },
    country: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    address1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    address2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    state: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    state_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    }
  }, {
    tableName: 'vw_LEWHWithIncorrectState'
  });
};

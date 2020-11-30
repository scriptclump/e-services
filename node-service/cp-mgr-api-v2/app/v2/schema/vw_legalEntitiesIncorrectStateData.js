/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_legalEntitiesIncorrectStateData', {
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    business_legal_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    legal_entity_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    business_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    address1: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    address2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    state_id: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    state_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    country: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    pincode: {
      type: DataTypes.STRING(12),
      allowNull: false
    },
    city_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    }
  }, {
    tableName: 'vw_legalEntitiesIncorrectStateData'
  });
};

/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('wh_locations', {
    wh_location_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    wh_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_name: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    contact_email: {
      type: DataTypes.STRING(255),
      allowNull: false,
      defaultValue: ''
    },
    contact_phone: {
      type: DataTypes.STRING(20),
      allowNull: false,
      defaultValue: ''
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    parent_location_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    location_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    address1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    address2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    state: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    country: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    pincode: {
      type: DataTypes.INTEGER(12),
      allowNull: true
    },
    region: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    longitude: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    latitude: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    erp_code: {
      type: DataTypes.STRING(13),
      allowNull: true
    },
    is_deleted: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'wh_locations'
  });
};

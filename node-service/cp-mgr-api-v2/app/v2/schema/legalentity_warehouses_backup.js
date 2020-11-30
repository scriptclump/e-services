/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('legalentity_warehouses_backup', {
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    le_wh_code: {
      type: DataTypes.STRING(16),
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
    lp_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dc_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sort_order: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bu_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cost_centre: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    tin_number: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    lp_wh_name: {
      type: DataTypes.STRING(100),
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
    pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(4),
      allowNull: true,
      defaultValue: '0'
    },
    longitude: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    latitude: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    landmark: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
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
    tableName: 'legalentity_warehouses_backup'
  });
};

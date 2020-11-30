/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vehicles', {
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    vehicle_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'legal_entities',
        key: 'legal_entity_id'
      }
    },
    vehicle_model_name: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    vehicle_reg_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    license_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    vehicle_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    model_year: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    },
    add1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    add2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    country: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    state: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    rm: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '1'
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
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
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'vehicles'
  });
};

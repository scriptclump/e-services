/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('business_units', {
    bu_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    bu_name: {
      type: DataTypes.STRING(65),
      allowNull: false
    },
    description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'legal_entities',
        key: 'legal_entity_id'
      }
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    is_state: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    cost_center: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    parent_bu_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    tally_company_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    sales_ledger_name: {
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
    tableName: 'business_units'
  });
};

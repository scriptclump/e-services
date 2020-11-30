/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('purchase_price_history', {
    pur_price_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'products',
        key: 'product_id'
      }
    },
    po_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    supplier_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'legal_entities',
        key: 'legal_entity_id'
      }
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'legalentity_warehouses',
        key: 'le_wh_id'
      }
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    actual_elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    effective_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'users',
        key: 'user_id'
      }
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'purchase_price_history'
  });
};

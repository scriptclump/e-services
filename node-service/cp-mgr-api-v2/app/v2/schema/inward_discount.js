/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inward_discount', {
    inward_discount_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    inward_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'inward',
        key: 'inward_id'
      }
    },
    bill_discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_per: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sub_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    scheme_ref_id: {
      type: DataTypes.INTEGER(11),
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
    tableName: 'inward_discount'
  });
};

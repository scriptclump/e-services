/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('shipping_charges', {
    shipping_charges_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'mp',
        key: 'mp_id'
      }
    },
    shipment_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    start_weight: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    end_weight: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    charge_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    intracity: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    regional: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    metro_to_metro: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    north_east: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    j_k: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    rest_of_india: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    uom: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    additional_weight_flag: {
      type: DataTypes.INTEGER(1),
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
    tableName: 'shipping_charges'
  });
};

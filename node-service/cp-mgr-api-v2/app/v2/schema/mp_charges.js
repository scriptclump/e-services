/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mp_charges', {
    mp_charges_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'mp',
        key: 'mp_id'
      }
    },
    mp_key: {
      type: DataTypes.STRING(4),
      allowNull: true
    },
    service_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'mp_service_type',
        key: 'service_type_id'
      }
    },
    charges_from_date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    charges_to_date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    ed_fee: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    charges: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    charge_type: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    is_recurring: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    recurring_interval: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    recurring_period: {
      type: DataTypes.CHAR(4),
      allowNull: false
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    deleted_at: {
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
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'mp_charges'
  });
};

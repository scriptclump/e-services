/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('le_charges', {
    le_charges_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    charges: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    mp_charges_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reference_id: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    ed_fee: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'mp',
        key: 'mp_id'
      }
    },
    service_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    paid_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    le_payout_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    entity_table_name_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    currency_id: {
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
    tableName: 'le_charges'
  });
};

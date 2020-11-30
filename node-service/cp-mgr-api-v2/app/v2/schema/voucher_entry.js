/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('voucher_entry', {
    voucher_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    voucher_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    voucher_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    voucher_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    total_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    narration: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    costcentre_costgroup: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    reference_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    tally_resp: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    sync_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    is_posted: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
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
    tableName: 'voucher_entry'
  });
};

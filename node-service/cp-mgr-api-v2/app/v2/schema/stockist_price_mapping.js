/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('stockist_price_mapping', {
    id: {
      type: DataTypes.BIGINT,
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.BIGINT,
      allowNull: true,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.BIGINT,
      allowNull: false
    },
    stockist_price_group_id: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'stockist_price_mapping'
  });
};

/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_freebie_return', {
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    free_prd_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    freebee_desc: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    nm: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    returnfree: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    expfree: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'vw_freebie_return'
  });
};
